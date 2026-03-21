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
        $content = $homeSection->content ?? [];

        // 1. Process string/array inputs
        foreach ($request->input('content', []) as $key => $value) {
            // If the field was an array (from JSON textarea), decode it back
            if (isset($content[$key]) && (is_array($content[$key]) || is_object($content[$key])) && is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }
            $content[$key] = $value;
        }

        // 2. Process file inputs
        $files = $request->file('content');
        if (is_array($files)) {
            foreach ($files as $key => $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
                    
                    $path = $file->storeAs('uploads/media', $filename, 'public');
                    
                    \App\Models\Media::create([
                        'filename' => $filename,
                        'path' => $path,
                        'disk' => 'public',
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'title' => $originalName,
                    ]);

                    $content[$key] = 'storage/' . $path;
                }
            }
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
