<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function index()
    {
        $sections = HomeSection::orderBy('order')->get();
        return view('admin.home-sections.index', compact('sections'));
    }

    public function edit(HomeSection $homeSection)
    {
        return view('admin.home-sections.edit', compact('homeSection'));
    }

    public function update(Request $request, HomeSection $homeSection)
    {
        // Validation logic will vary based on section key. 
        // For now, we'll allow all keys in the content array to be updated.
        $content = $homeSection->content;

        foreach ($request->input('content', []) as $key => $value) {
            // Handle file uploads if any (e.g., image replacement)
            if ($request->hasFile("content.$key")) {
                $file = $request->file("content.$key");
                $path = $file->store('home-sections', 'public');
                $value = $path;
            }
            $content[$key] = $value;
        }

        $homeSection->update([
            'content' => $content,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.home-sections.index')->with('success', 'Section updated successfully.');
    }

    public function updateOrder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $id => $order) {
            HomeSection::where('id', $id)->update(['order' => $order]);
        }
        return response()->json(['success' => true]);
    }

    public function toggleStatus(HomeSection $homeSection)
    {
        $homeSection->update([
            'status' => $homeSection->status === 'published' ? 'draft' : 'published'
        ]);
        return back()->with('success', 'Status updated successfully.');
    }
}
