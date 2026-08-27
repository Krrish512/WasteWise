<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
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
            $destinationPath = public_path('uploads/announcements');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/announcements/' . $filename;
        }

        $slug = $this->generateUniqueSlug($validated['title']);

        Announcement::create([
            'title'        => $validated['title'],
            'slug'         => $slug,
            'content'      => $validated['content'],
            'image'        => $imagePath,
            'published_at' => $publishedAt,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $isPublished = $request->has('is_published') ? (bool) $request->input('is_published') : false;

        $publishedAt = $validated['published_at'] ?? $announcement->published_at;
        if ($isPublished && !$publishedAt) {
            $publishedAt = now();
        }

        $imagePath = $announcement->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if exists
            if ($announcement->image && file_exists(public_path($announcement->image))) {
                @unlink(public_path($announcement->image));
            }

            $file = $request->file('image');
            $extension = $file->extension();
            $filename = Str::random(30) . '.' . $extension;
            $destinationPath = public_path('uploads/announcements');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/announcements/' . $filename;
        }

        $slug = $announcement->slug;
        if ($announcement->title !== $validated['title']) {
            $slug = $this->generateUniqueSlug($validated['title'], $announcement->id);
        }

        $announcement->update([
            'title'        => $validated['title'],
            'slug'         => $slug,
            'content'      => $validated['content'],
            'image'        => $imagePath,
            'published_at' => $publishedAt,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->image && file_exists(public_path($announcement->image))) {
            @unlink(public_path($announcement->image));
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Toggle published status of announcement.
     */
    public function togglePublished(Announcement $announcement)
    {
        $newPublishedState = !$announcement->is_published;
        $publishedAt = $announcement->published_at;

        if ($newPublishedState && !$publishedAt) {
            $publishedAt = now();
        }

        $announcement->update([
            'is_published' => $newPublishedState,
            'published_at' => $publishedAt,
        ]);

        $statusLabel = $newPublishedState ? 'published' : 'unpublished';
        return redirect()->back()
            ->with('success', "Announcement '{$announcement->title}' has been {$statusLabel}.");
    }

    /**
     * Generate unique slug for title.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        if (empty($slug)) {
            $slug = 'announcement';
        }
        $originalSlug = $slug;
        $count = 1;

        while (Announcement::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
