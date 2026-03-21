<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailList;
use Illuminate\Http\Request;

class EmailListController extends Controller
{
    public function index()
    {
        $lists = EmailList::withCount('contacts')->latest()->paginate(20);
        return view('admin.email-lists.index', compact('lists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        EmailList::create($validated);
        return redirect()->route('admin.email-lists.index')->with('success', 'Email list created successfully.');
    }

    public function show(EmailList $emailList)
    {
        $contacts = $emailList->contacts()->latest()->paginate(50);
        return view('admin.email-lists.show', compact('emailList', 'contacts'));
    }

    public function update(Request $request, EmailList $emailList)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $emailList->update($validated);
        return redirect()->route('admin.email-lists.index')->with('success', 'Email list updated successfully.');
    }

    public function destroy(EmailList $emailList)
    {
        $emailList->delete();
        return redirect()->route('admin.email-lists.index')->with('success', 'Email list deleted successfully.');
    }
}
