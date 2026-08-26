<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of waste categories.
     */
    public function index()
    {
        $categories = WasteCategory::withCount(['wasteItems', 'collectionCentres'])
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:waste_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        WasteCategory::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Waste category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(WasteCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, WasteCategory $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:waste_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Waste category updated successfully!');
    }

    /**
     * Remove the specified category after checking dependencies.
     */
    public function destroy(WasteCategory $category)
    {
        $itemsCount   = $category->wasteItems()->count();
        $centresCount = $category->collectionCentres()->count();

        if ($itemsCount > 0 || $centresCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Cannot delete category '{$category->name}'. It is currently referenced by {$itemsCount} waste item(s) and {$centresCount} collection centre(s).");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Waste category '{$category->name}' deleted successfully!");
    }
}
