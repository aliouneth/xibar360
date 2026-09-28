<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::latest()->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        Category::create($request->validated());

        return redirect()->route('admin.categories.index')->with('success', __('Category created successfully.'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('success', __('Category updated successfully.'));
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', __('Category deleted successfully.'));
    }
}
