<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisposalMethod;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Http\Request;

class WasteItemController extends Controller
{
    /**
     * Display a listing of waste items.
     */
    public function index(Request $request)
    {
        $query = WasteItem::with(['category', 'disposalMethod']);

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('keywords', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items      = $query->orderBy('name')->get();
        $categories = WasteCategory::orderBy('name')->get();

        return view('admin.waste_items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new waste item.
     */
    public function create()
    {
        $categories = WasteCategory::orderBy('name')->get();
        $methods    = DisposalMethod::orderBy('name')->get();

        return view('admin.waste_items.create', compact('categories', 'methods'));
    }

    /**
     * Store a newly created waste item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255|unique:waste_items,name',
            'category_id'        => 'required|exists:waste_categories,id',
            'disposal_method_id' => 'required|exists:disposal_methods,id',
            'description'        => 'nullable|string|max:2000',
            'keywords'           => 'nullable|string|max:1000',
        ]);

        WasteItem::create($validated);

        return redirect()->route('admin.waste-items.index')
            ->with('success', 'Waste item created successfully!');
    }

    /**
     * Show the form for editing the specified waste item.
     */
    public function edit(WasteItem $wasteItem)
    {
        $categories = WasteCategory::orderBy('name')->get();
        $methods    = DisposalMethod::orderBy('name')->get();

        return view('admin.waste_items.edit', [
            'item'       => $wasteItem,
            'categories' => $categories,
            'methods'    => $methods,
        ]);
    }

    /**
     * Update the specified waste item.
     */
    public function update(Request $request, WasteItem $wasteItem)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255|unique:waste_items,name,' . $wasteItem->id,
            'category_id'        => 'required|exists:waste_categories,id',
            'disposal_method_id' => 'required|exists:disposal_methods,id',
            'description'        => 'nullable|string|max:2000',
            'keywords'           => 'nullable|string|max:1000',
        ]);

        $wasteItem->update($validated);

        return redirect()->route('admin.waste-items.index')
            ->with('success', 'Waste item updated successfully!');
    }

    /**
     * Remove the specified waste item.
     */
    public function destroy(WasteItem $wasteItem)
    {
        $itemName = $wasteItem->name;
        $wasteItem->delete();

        return redirect()->route('admin.waste-items.index')
            ->with('success', "Waste item '{$itemName}' deleted successfully!");
    }
}
