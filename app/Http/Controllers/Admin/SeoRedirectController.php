<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SeoRedirect;

class SeoRedirectController extends Controller
{
    public function index()
    {
        $redirects = SeoRedirect::latest()->paginate(20);
        return view('admin.seo.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('admin.seo.redirects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_path' => 'required|string|unique:seo_redirects,from_path',
            'to_path' => 'required|string',
            'type' => 'required|in:301,302',
        ]);

        SeoRedirect::create($request->all());
        return redirect()->route('admin.seo.redirects.index')->with('success', 'Redirect added.');
    }

    public function edit(SeoRedirect $redirect)
    {
        return view('admin.seo.redirects.edit', compact('redirect'));
    }

    public function update(Request $request, SeoRedirect $redirect)
    {
        $request->validate([
            'from_path' => "required|string|unique:seo_redirects,from_path,{$redirect->id}",
            'to_path' => 'required|string',
            'type' => 'required|in:301,302',
        ]);

        $redirect->update($request->all());
        return redirect()->route('admin.seo.redirects.index')->with('success', 'Redirect updated.');
    }

    public function destroy(SeoRedirect $redirect)
    {
        $redirect->delete();
        return redirect()->back()->with('success', 'Redirect deleted.');
    }
}
