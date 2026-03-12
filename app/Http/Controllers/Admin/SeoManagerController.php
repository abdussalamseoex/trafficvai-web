<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;
use App\Models\Service;
use App\Models\Post;
use App\Models\Category;
use App\Models\SeoMeta;

class SeoManagerController extends Controller
{
    public function index()
    {
        $stats = [
            'pages' => Page::count(),
            'services' => Service::count(),
            'posts' => Post::count(),
            'categories' => Category::count(),
        ];

        return view('admin.seo.index', compact('stats'));
    }

    public function pages()
    {
        $items = Page::with('seoMeta')->paginate(15);
        $type = 'Page';
        return view('admin.seo.list', compact('items', 'type'));
    }

    public function services()
    {
        $items = Service::with('seoMeta')->paginate(15);
        $type = 'Service';
        return view('admin.seo.list', compact('items', 'type'));
    }

    public function posts()
    {
        $items = Post::with('seoMeta')->paginate(15);
        $type = 'Post';
        return view('admin.seo.list', compact('items', 'type'));
    }

    public function categories()
    {
        $items = Category::with('seoMeta')->paginate(15);
        $type = 'Category';
        return view('admin.seo.list', compact('items', 'type'));
    }

    public function edit($type, $id)
    {
        $modelClass = "App\\Models\\{$type}";
        $item = $modelClass::findOrFail($id);
        $seo = $item->seoMeta ?? new SeoMeta();

        return view('admin.seo.edit', compact('item', 'seo', 'type', 'id'));
    }

    public function update(Request $request, $type, $id)
    {
        $modelClass = "App\\Models\\{$type}";
        $item = $modelClass::findOrFail($id);

        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'slug' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url',
            'focus_keyword' => 'nullable|string',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
            'featured_image' => 'nullable|image|max:2048',
            'image_alt_text' => 'nullable|string',
            'breadcrumb_title' => 'nullable|string',
            'robots_index' => 'nullable|boolean',
            'robots_directive' => 'nullable|string',
            'schema_json' => 'nullable|string',
            'publish_date' => 'nullable|date',
        ]);

        $data = $request->except(['og_image', 'featured_image', '_token']);

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('seo', 'public');
        }
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('seo', 'public');
        }

        $item->seoMeta()->updateOrCreate([], $data);

        return redirect()->back()->with('success', 'SEO settings updated successfully.');
    }
}
