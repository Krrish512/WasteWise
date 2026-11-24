<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CollectionCentreController extends Controller
{
    /**
     * Display a listing of collection centres.
     */
    public function index()
    {
        $centres = CollectionCentre::with(['city', 'category', 'categories'])
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
            'name'                      => 'required|string|max:255',
            'city_id'                   => 'required|exists:cities,id',
            'district'                  => 'nullable|string|max:255',
            'category_id'               => 'nullable|exists:waste_categories,id',
            'category_ids'              => 'nullable|array',
            'category_ids.*'            => 'exists:waste_categories,id',
            'address'                   => 'required|string|max:1000',
            'phone'                     => 'nullable|string|max:50',
            'latitude'                  => 'nullable|numeric|between:-90,90',
            'longitude'                 => 'nullable|numeric|between:-180,180',
            'facility_type'             => ['nullable', 'string', Rule::in([
                'public_dropoff', 'municipal_centre', 'authorized_recycler',
                'processing_facility', 'pickup_provider', 'industrial_facility'
            ])],
            'public_access'             => ['nullable', 'string', Rule::in([
                'public_dropoff', 'business_industrial_only', 'pickup_only', 'unclear'
            ])],
            'authorization_status'      => ['nullable', 'string', Rule::in([
                'verified', 'needs_confirmation', 'expired', 'unknown'
            ])],
            'authorization_id'          => 'nullable|string|max:255',
            'authorization_valid_until' => 'nullable|date',
            'website'                   => 'nullable|string|max:255',
            'source_url'                => 'nullable|string|max:500',
            'verification_date'         => 'nullable|date',
            'confidence'                => ['nullable', 'string', Rule::in(['high', 'medium', 'low'])],
            'status'                    => ['nullable', 'string', Rule::in(['active', 'pending_verification', 'archived'])],
            'notes'                     => 'nullable|string|max:5000',
        ]);

        // Resolve categories (backward compatibility: supports either category_id or category_ids array)
        $categoryIds = $request->input('category_ids', []);
        if (!is_array($categoryIds)) {
            $categoryIds = array_filter([$categoryIds]);
        }
        if (!empty($validated['category_id']) && !in_array($validated['category_id'], $categoryIds)) {
            array_unshift($categoryIds, $validated['category_id']);
        }
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        if (empty($categoryIds)) {
            return back()->withErrors(['category_id' => 'Please select at least one waste category.'])->withInput();
        }

        // Set primary single category_id for backward compatibility
        $validated['category_id'] = $categoryIds[0];
        $validated['facility_type'] = $validated['facility_type'] ?? 'public_dropoff';
        $validated['public_access'] = $validated['public_access'] ?? 'public_dropoff';
        $validated['authorization_status'] = $validated['authorization_status'] ?? 'verified';
        $validated['confidence'] = $validated['confidence'] ?? 'medium';
        $validated['status'] = $validated['status'] ?? 'active';

        unset($validated['category_ids']);

        $centre = CollectionCentre::create($validated);
        $centre->categories()->sync($categoryIds);

        return redirect()->route('admin.collection-centres.index')
            ->with('success', 'Collection centre created successfully!');
    }

    /**
     * Show the form for editing the specified collection centre.
     */
    public function edit(CollectionCentre $collectionCentre)
    {
        $collectionCentre->load(['categories', 'category', 'city']);
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
            'name'                      => 'required|string|max:255',
            'city_id'                   => 'required|exists:cities,id',
            'district'                  => 'nullable|string|max:255',
            'category_id'               => 'nullable|exists:waste_categories,id',
            'category_ids'              => 'nullable|array',
            'category_ids.*'            => 'exists:waste_categories,id',
            'address'                   => 'required|string|max:1000',
            'phone'                     => 'nullable|string|max:50',
            'latitude'                  => 'nullable|numeric|between:-90,90',
            'longitude'                 => 'nullable|numeric|between:-180,180',
            'facility_type'             => ['nullable', 'string', Rule::in([
                'public_dropoff', 'municipal_centre', 'authorized_recycler',
                'processing_facility', 'pickup_provider', 'industrial_facility'
            ])],
            'public_access'             => ['nullable', 'string', Rule::in([
                'public_dropoff', 'business_industrial_only', 'pickup_only', 'unclear'
            ])],
            'authorization_status'      => ['nullable', 'string', Rule::in([
                'verified', 'needs_confirmation', 'expired', 'unknown'
            ])],
            'authorization_id'          => 'nullable|string|max:255',
            'authorization_valid_until' => 'nullable|date',
            'website'                   => 'nullable|string|max:255',
            'source_url'                => 'nullable|string|max:500',
            'verification_date'         => 'nullable|date',
            'confidence'                => ['nullable', 'string', Rule::in(['high', 'medium', 'low'])],
            'status'                    => ['nullable', 'string', Rule::in(['active', 'pending_verification', 'archived'])],
            'notes'                     => 'nullable|string|max:5000',
        ]);

        $categoryIds = $request->input('category_ids', []);
        if (!is_array($categoryIds)) {
            $categoryIds = array_filter([$categoryIds]);
        }
        if (!empty($validated['category_id']) && !in_array($validated['category_id'], $categoryIds)) {
            array_unshift($categoryIds, $validated['category_id']);
        }
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        if (empty($categoryIds)) {
            return back()->withErrors(['category_id' => 'Please select at least one waste category.'])->withInput();
        }

        $validated['category_id'] = $categoryIds[0];
        $validated['facility_type'] = $validated['facility_type'] ?? $collectionCentre->facility_type ?? 'public_dropoff';
        $validated['public_access'] = $validated['public_access'] ?? $collectionCentre->public_access ?? 'public_dropoff';
        $validated['authorization_status'] = $validated['authorization_status'] ?? $collectionCentre->authorization_status ?? 'verified';
        $validated['confidence'] = $validated['confidence'] ?? $collectionCentre->confidence ?? 'medium';
        $validated['status'] = $validated['status'] ?? $collectionCentre->status ?? 'active';

        unset($validated['category_ids']);

        $collectionCentre->update($validated);
        $collectionCentre->categories()->sync($categoryIds);

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
