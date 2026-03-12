<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Display a listing of the free SEO tools.
     */
    public function index()
    {
        return view('client.tools.index');
    }

    /**
     * Handle a mock submission for the Website SEO Audit.
     */
    public function submitAudit(Request $request)
    {
        $request->validate([
            'website_url' => 'required|url',
        ]);

        // In a real scenario, this would trigger a job to generate an audit report
        // or send an email to the admin to do it manually.

        return redirect()->route('client.tools.index')
            ->with('success', 'Audit request received for ' . $request->website_url . '. We will email you the full report shortly!');
    }
}
