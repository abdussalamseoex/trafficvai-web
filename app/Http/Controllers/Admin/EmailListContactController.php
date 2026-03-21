<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailList;
use App\Models\EmailListContact;
use Illuminate\Http\Request;

class EmailListContactController extends Controller
{
    public function store(Request $request, EmailList $emailList)
    {
        $request->validate([
            'emails' => 'required|string',
        ]);

        // Parse emails by comma or newline
        $rawEmails = preg_split('/[,\n]+/', $request->emails);
        $validEmails = [];
        foreach ($rawEmails as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            }
        }

        // Get existing emails in the list to prevent duplicates
        $existingEmails = $emailList->contacts()->pluck('email')->toArray();
        $validEmails = array_diff(array_unique($validEmails), $existingEmails);

        // Bulk insert
        $insertData = [];
        $now = now();
        foreach ($validEmails as $email) {
            $insertData[] = [
                'email_list_id' => $emailList->id,
                'email' => $email,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($insertData)) {
            EmailListContact::insert($insertData);
        }

        return redirect()->route('admin.email-lists.show', $emailList)->with('success', count($insertData) . ' valid non-duplicate emails added.');
    }

    public function destroy(EmailList $emailList, EmailListContact $contact)
    {
        if ($contact->email_list_id == $emailList->id) {
            $contact->delete();
        }
        return redirect()->route('admin.email-lists.show', $emailList)->with('success', 'Contact removed successfully.');
    }
}
