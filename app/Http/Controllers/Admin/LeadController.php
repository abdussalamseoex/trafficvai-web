<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leads = \App\Models\Lead::latest()->paginate(20);
        return view('admin.leads.index', compact('leads'));
    }

    public function show(\App\Models\Lead $lead)
    {
        return view('admin.leads.show', compact('lead'));
    }

    public function update(Request $request, \App\Models\Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,closed',
        ]);

        $lead->update($validated);

        return back()->with('success', 'Lead status updated successfully.');
    }

    public function destroy(\App\Models\Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully.');
    }
}
