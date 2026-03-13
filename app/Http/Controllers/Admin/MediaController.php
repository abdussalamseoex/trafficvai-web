<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a listing of the media.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Media::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(24);

        return view('admin.media.index', compact('media'));
    }

    /**
     * Store a newly created media in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:10240', // 10MB Max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            // Create SEO-friendly filename: time_original-name-slug.jpg
            $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;
            
            $path = $file->storeAs('uploads/media', $filename, 'public');

            $media = Media::create([
                'filename' => $filename,
                'path' => $path,
                'disk' => 'public',
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'title' => $originalName,
            ]);

            return response()->json([
                'success' => true,
                'media' => $media,
                'url' => $media->url
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Sync existing storage files to media library.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync()
    {
        \Illuminate\Support\Facades\Artisan::call('media:sync');
        
        return back()->with('success', 'Media library synchronized with storage.');
    }

    /**
     * Update the specified media in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $media->update($request->only(['alt_text', 'title', 'description']));

        return back()->with('success', 'Media metadata updated successfully.');
    }

    /**
     * Remove the specified media from storage.
     * 
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Media $media)
    {
        if ($media->path && Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();

        return back()->with('success', 'Media deleted successfully.');
    }

    /**
     * Remove multiple media from storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        $media = Media::whereIn('id', $request->ids)->get();
        
        foreach ($media as $item) {
            if ($item->path && Storage::disk($item->disk)->exists($item->path)) {
                Storage::disk($item->disk)->delete($item->path);
            }
            $item->delete();
        }

        return back()->with('success', count($media) . ' items deleted successfully.');
    }
}
