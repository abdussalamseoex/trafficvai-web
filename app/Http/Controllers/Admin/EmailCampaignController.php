<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Jobs\SendCustomBulkEmailJob;
use App\Mail\CustomPromotionalEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailCampaignController extends Controller
{
    public function index()
    {
        $campaigns = EmailCampaign::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.bulk-emails.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.bulk-emails.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'emails' => 'required|string',
        ]);

        // Extract and validate emails
        $rawEmails = preg_split('/[,\n\r]+/', $request->input('emails'));
        $rawEmails = array_map('trim', $rawEmails);
        $validEmails = array_filter($rawEmails, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        $validEmails = array_values(array_unique($validEmails));

        if (count($validEmails) === 0) {
            return back()->withInput()->withErrors(['emails' => 'No valid email addresses were found in the list.']);
        }

        // Store campaign record
        $campaign = EmailCampaign::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'recipient_count' => count($validEmails),
            'recipients' => implode(', ', $validEmails), // Can be null or truncated if needed, but safe here
            'status' => 'processing',
        ]);

        // Dispatch Jobs immediately (the delay will happen inside the job's execution layer)
        foreach ($validEmails as $email) {
            SendCustomBulkEmailJob::dispatch($email, $campaign->id);
        }

        $campaign->update(['status' => 'completed_queueing']);

        // Spawn a background queue worker for shared hosting without Supervisor/Cron
        $basePath = base_path();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B php {$basePath}\artisan queue:work --stop-when-empty", "r"));
        } else {
            exec("nohup php {$basePath}/artisan queue:work --stop-when-empty > /dev/null 2>&1 &");
        }

        return redirect()->route('admin.bulk-emails.index')->with('success', count($validEmails) . ' promotional emails have been queued for sending.');
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $testEmail = auth()->user()->email;

        try {
            // Apply Dynamic DB SMTP settings before sending
            app(\App\Services\NotificationService::class)->applyMailConfig();

            $logo = \App\Models\Setting::get('site_logo');
            $logoUrl = $logo ? asset($logo) : (config('app.url') . '/images/logo.png');
            
            $renderedHtml = view('emails.v2.universal_v2', [
                'title' => $request->subject,
                'body' => $request->message,
                'message' => $request->message,
                'tag' => 'SPECIAL OFFER',
                'user_name' => 'Valued Client',
                'logo_url' => $logoUrl,
                'dashboard_portal_url' => url('/dashboard'),
            ])->render();

            Mail::to($testEmail)->send(new \App\Mail\DynamicNotificationMail($request->subject, $renderedHtml));
            return response()->json(['message' => 'Test email sent successfully to ' . $testEmail]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send test email: ' . $e->getMessage()], 500);
        }
    }
}
