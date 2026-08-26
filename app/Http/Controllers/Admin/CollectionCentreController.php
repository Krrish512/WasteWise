<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\WasteCategory;
use Illuminate\Http\Request;

class CollectionCentreController extends Controller
{
    /**
     * Display a listing of collection centres.
     */
    public function index()
    {
        $centres = CollectionCentre::with(['city', 'category'])
            ->orderBy('name')
            ->get();

        return view('admin.collection_centres.index', compact('centres'));
    }

    /**
     * Show the form for creating a new collection centre.
     */
    public function create()
    {
        $cities     = City::orderBy('name')->get();
        $categories = WasteCategory::orderBy('name')->get();

        return view('admin.collection_centres.create', compact('cities', 'categories'));
    }

    /**
     * Store a newly created collection centre.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'city_id'     => 'required|exists:cities,id',
            'category_id' => 'required|exists:waste_categories,id',
            'address'     => 'required|string|max:1000',
            'phone'       => 'nullable|string|max:50',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        CollectionCentre::create($validated);

        return redirect()->route('admin.collection-centres.index')
            ->with('success', 'Collection centre created successfully!');
    }

    /**
     * Show the form for editing the specified collection centre.
     */
    public function edit(CollectionCentre $collectionCentre)
    {
        $cities     = City::orderBy('name')->get();
        $categories = WasteCategory::orderBy('name')->get();

        return view('admin.collection_centres.edit', [
            'centre'     => $collectionCentre,
            'cities'     => $cities,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified collection centre.
     */
    public function update(Request $request, CollectionCentre $collectionCentre)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'city_id'     => 'required|exists:cities,id',
            'category_id' => 'required|exists:waste_categories,id',
            'address'     => 'required|string|max:1000',
            'phone'       => 'nullable|string|max:50',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        $collectionCentre->update($validated);

        return redirect()->route('admin.collection-centres.index')
            ->with('success', 'Collection centre updated successfully!');
    }

    /**
     * Remove the specified collection centre.
     */
    public function destroy(CollectionCentre $collectionCentre)
    {
        $centreName = $collectionCentre->name;
        $collectionCentre->delete();

        return redirect()->route('admin.collection-centres.index')
            ->with('success', "Collection centre '{$centreName}' deleted successfully!");
    }
}
