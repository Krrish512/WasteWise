<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisposalMethod;
use Illuminate\Http\Request;

class DisposalMethodController extends Controller
{
    /**
     * Display a listing of disposal methods.
     */
    public function index()
    {
        $methods = DisposalMethod::withCount('wasteItems')
            ->orderBy('name')
            ->get();

        return view('admin.disposal_methods.index', compact('methods'));
    }

    /**
     * Show the form for creating a new disposal method.
     */
    public function create()
    {
        return view('admin.disposal_methods.create');
    }

    /**
     * Store a newly created disposal method.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:disposal_methods,name',
            'instructions' => 'required|string|max:2000',
            'avoid'        => 'nullable|string|max:2000',
        ]);

        DisposalMethod::create($validated);

        return redirect()->route('admin.disposal-methods.index')
            ->with('success', 'Disposal method created successfully!');
    }

    /**
     * Show the form for editing the specified disposal method.
     */
    public function edit(DisposalMethod $disposalMethod)
    {
        return view('admin.disposal_methods.edit', ['method' => $disposalMethod]);
    }

    /**
     * Update the specified disposal method.
     */
    public function update(Request $request, DisposalMethod $disposalMethod)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:disposal_methods,name,' . $disposalMethod->id,
            'instructions' => 'required|string|max:2000',
            'avoid'        => 'nullable|string|max:2000',
        ]);

        $disposalMethod->update($validated);

        return redirect()->route('admin.disposal-methods.index')
            ->with('success', 'Disposal method updated successfully!');
    }

    /**
     * Remove the specified disposal method after checking dependencies.
     */
    public function destroy(DisposalMethod $disposalMethod)
    {
        $itemsCount = $disposalMethod->wasteItems()->count();

        if ($itemsCount > 0) {
            return redirect()->route('admin.disposal-methods.index')
                ->with('error', "Cannot delete disposal method '{$disposalMethod->name}'. It is currently referenced by {$itemsCount} waste item(s).");
        }

        $disposalMethod->delete();

        return redirect()->route('admin.disposal-methods.index')
            ->with('success', "Disposal method '{$disposalMethod->name}' deleted successfully!");
    }
}
