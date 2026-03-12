<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('admin.notifications.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.notifications.templates.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:email_templates',
            'name' => 'required',
            'subject' => 'required',
            'body' => 'required',
        ]);

        EmailTemplate::create($request->all());
        return redirect()->route('admin.notifications.templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(EmailTemplate $template)
    {
        return view('admin.notifications.templates.edit', compact('template'));
    }

    public function update(\Illuminate\Http\Request $request, EmailTemplate $template)
    {
        $request->validate([
            'slug' => 'required|unique:email_templates,slug,' . $template->id,
            'name' => 'required',
            'subject' => 'required',
            'body' => 'required',
        ]);

        $template->update($request->all());
        return redirect()->route('admin.notifications.templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(EmailTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.notifications.templates.index')->with('success', 'Template deleted successfully.');
    }
}
