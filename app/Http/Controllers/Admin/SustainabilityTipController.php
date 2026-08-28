<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SustainabilityTip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SustainabilityTipController extends Controller
{
    /**
     * Display a listing of sustainability tips.
     */
    public function index(Request $request)
    {
        $query = SustainabilityTip::query();

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $tips = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = SustainabilityTip::distinct()->pluck('category')->filter()->values();

        return view('admin.sustainability_tips.index', compact('tips', 'categories'));
    }

    /**
     * Show the form for creating a new sustainability tip.
     */
    public function create()
    {
        $existingCategories = SustainabilityTip::distinct()->pluck('category')->filter()->values();
        return view('admin.sustainability_tips.create', compact('existingCategories'));
    }

    /**
     * Store a newly created sustainability tip in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'summary'      => 'nullable|string|max:1000',
            'content'      => 'required|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $isPublished = $request->has('is_published') ? (bool) $request->input('is_published') : false;
        $publishedAt = $validated['published_at'] ?? null;
        if ($isPublished && !$publishedAt) {
            $publishedAt = now();
        }

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $extension = $file->extension();
            $filename = Str::random(30) . '.' . $extension;
            $destinationPath = public_path('uploads/sustainability_tips');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/sustainability_tips/' . $filename;
        }

        $slug = $this->generateUniqueSlug($validated['title']);

        SustainabilityTip::create([
            'title'        => $validated['title'],
            'slug'         => $slug,
            'category'     => trim($validated['category']),
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'image'        => $imagePath,
            'published_at' => $publishedAt,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.sustainability-tips.index')
            ->with('success', 'Sustainability tip created successfully.');
    }

    /**
     * Show the form for editing the specified sustainability tip.
     */
    public function edit(SustainabilityTip $sustainabilityTip)
    {
        $existingCategories = SustainabilityTip::distinct()->pluck('category')->filter()->values();
        return view('admin.sustainability_tips.edit', [
            'tip'                => $sustainabilityTip,
            'existingCategories' => $existingCategories,
        ]);
    }

    /**
     * Update the specified sustainability tip in storage.
     */
    public function update(Request $request, SustainabilityTip $sustainabilityTip)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'summary'      => 'nullable|string|max:1000',
            'content'      => 'required|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $isPublished = $request->has('is_published') ? (bool) $request->input('is_published') : false;
        $publishedAt = $validated['published_at'] ?? $sustainabilityTip->published_at;
        if ($isPublished && !$publishedAt) {
            $publishedAt = now();
        }

        $imagePath = $sustainabilityTip->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($sustainabilityTip->image && file_exists(public_path($sustainabilityTip->image))) {
                @unlink(public_path($sustainabilityTip->image));
            }

            $file = $request->file('image');
            $extension = $file->extension();
            $filename = Str::random(30) . '.' . $extension;
            $destinationPath = public_path('uploads/sustainability_tips');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/sustainability_tips/' . $filename;
        }

        $slug = $sustainabilityTip->slug;
        if ($sustainabilityTip->title !== $validated['title']) {
            $slug = $this->generateUniqueSlug($validated['title'], $sustainabilityTip->id);
        }

        $sustainabilityTip->update([
            'title'        => $validated['title'],
            'slug'         => $slug,
            'category'     => trim($validated['category']),
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'image'        => $imagePath,
            'published_at' => $publishedAt,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.sustainability-tips.index')
            ->with('success', 'Sustainability tip updated successfully.');
    }

    /**
     * Remove the specified sustainability tip from storage.
     */
    public function destroy(SustainabilityTip $sustainabilityTip)
    {
        if ($sustainabilityTip->image && file_exists(public_path($sustainabilityTip->image))) {
            @unlink(public_path($sustainabilityTip->image));
        }

        $sustainabilityTip->delete();

        return redirect()->route('admin.sustainability-tips.index')
            ->with('success', 'Sustainability tip deleted successfully.');
    }

    /**
     * Toggle published status of sustainability tip.
     */
    public function togglePublished(SustainabilityTip $sustainabilityTip)
    {
        $newPublishedState = !$sustainabilityTip->is_published;
        $publishedAt = $sustainabilityTip->published_at;

        if ($newPublishedState && !$publishedAt) {
            $publishedAt = now();
        }

        $sustainabilityTip->update([
            'is_published' => $newPublishedState,
            'published_at' => $publishedAt,
        ]);

        $statusLabel = $newPublishedState ? 'published' : 'unpublished';
        return redirect()->back()
            ->with('success', "Sustainability tip '{$sustainabilityTip->title}' has been {$statusLabel}.");
    }

    /**
     * Generate unique slug for title.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        if (empty($slug)) {
            $slug = 'sustainability-tip';
        }
        $originalSlug = $slug;
        $count = 1;

        while (SustainabilityTip::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
