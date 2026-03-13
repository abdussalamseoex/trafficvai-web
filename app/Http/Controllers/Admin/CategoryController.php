<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Traits\HandlesSeoMetadata;

class CategoryController extends Controller
{
    use HandlesSeoMetadata;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('parent')->withCount('services')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'type' => 'required|in:service,post',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'is_active' => $request->has('is_active'),
        ]);

        $this->syncSeoMetadata($category, $request);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load(['services', 'posts', 'parent', 'children']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'type' => 'required|in:service,post',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'is_active' => $request->has('is_active'),
        ]);

        $this->syncSeoMetadata($category, $request);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
