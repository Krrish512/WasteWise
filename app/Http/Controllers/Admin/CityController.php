<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of cities.
     */
    public function index()
    {
        $cities = City::withCount('collectionCentres')
            ->orderBy('name')
            ->get();

        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new city.
     */
    public function create()
    {
        return view('admin.cities.create');
    }

    /**
     * Store a newly created city.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
        ]);

        City::create($validated);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City created successfully!');
    }

    /**
     * Show the form for editing the specified city.
     */
    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    /**
     * Update the specified city.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
        ]);

        $city->update($validated);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City updated successfully!');
    }

    /**
     * Remove the specified city after checking dependencies.
     */
    public function destroy(City $city)
    {
        $centresCount = $city->collectionCentres()->count();

        if ($centresCount > 0) {
            return redirect()->route('admin.cities.index')
                ->with('error', "Cannot delete city '{$city->name}'. It is currently referenced by {$centresCount} collection centre(s).");
        }

        $city->delete();

        return redirect()->route('admin.cities.index')
            ->with('success', "City '{$city->name}' deleted successfully!");
    }
}
