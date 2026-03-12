<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Traits\HandlesSeoMetadata;

class PostController extends Controller
{
    use HandlesSeoMetadata;

    public function index()
    {
        $posts = Post::with('category')->latest()->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->where('type', 'post')->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->except(['featured_image', 'meta_title', 'meta_description', 'meta_keywords', 'focus_keyword', 'canonical_url', 'og_title', 'og_description', 'og_image', 'image_alt_text', 'breadcrumb_title', 'robots_directive', 'robots_index', 'schema_json', 'publish_date']);
        $data['slug'] = Str::slug($request->title);

        // Ensure unique slug
        $count = Post::where('slug', 'LIKE', $data['slug'] . '%')->count();
        if ($count > 0) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog/images', 'public');
        }

        $post = Post::create($data);
        $this->syncSeoMetadata($post, $request);

        return redirect()->route('admin.posts.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Post $post)
    {
        $categories = Category::where('is_active', true)->where('type', 'post')->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->except(['featured_image', 'meta_title', 'meta_description', 'meta_keywords', 'focus_keyword', 'canonical_url', 'og_title', 'og_description', 'og_image', 'image_alt_text', 'breadcrumb_title', 'robots_directive', 'robots_index', 'schema_json', 'publish_date']);
        $data['slug'] = Str::slug($request->title);

        $count = Post::where('slug', 'LIKE', $data['slug'] . '%')->where('id', '!=', $post->id)->count();
        if ($count > 0) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog/images', 'public');
        }

        $post->update($data);
        $this->syncSeoMetadata($post, $request);

        return redirect()->route('admin.posts.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Blog post deleted successfully.');
    }
}
