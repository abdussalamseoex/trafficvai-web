<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Traits\HandlesSeoMetadata;

class PageController extends Controller
{
    use HandlesSeoMetadata;

    public function index()
    {
        $pages = Page::latest()->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['meta_title', 'meta_description', 'meta_keywords', 'focus_keyword', 'canonical_url', 'og_title', 'og_description', 'og_image', 'image_alt_text', 'breadcrumb_title', 'robots_directive', 'robots_index', 'schema_json', 'publish_date']);
        $data['slug'] = Str::slug($request->title);
        $data['is_active'] = $request->has('is_active');

        // Ensure unique slug
        $count = Page::where('slug', 'LIKE', $data['slug'] . '%')->count();
        if ($count > 0) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

        $page = Page::create($data);
        $this->syncSeoMetadata($page, $request);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        $data = $request->except(['meta_title', 'meta_description', 'meta_keywords', 'focus_keyword', 'canonical_url', 'og_title', 'og_description', 'og_image', 'image_alt_text', 'breadcrumb_title', 'robots_directive', 'robots_index', 'schema_json', 'publish_date']);
        if ($request->title !== $page->title) {
            $data['slug'] = Str::slug($request->title);
            $count = Page::where('slug', 'LIKE', $data['slug'] . '%')->where('id', '!=', $page->id)->count();
            if ($count > 0) {
                $data['slug'] = $data['slug'] . '-' . time();
            }
        }

        $data['is_active'] = $request->has('is_active');

        $page->update($data);
        $this->syncSeoMetadata($page, $request);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }
}
