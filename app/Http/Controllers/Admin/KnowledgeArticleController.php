<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeArticleController extends Controller
{
    /**
     * Display a listing of knowledge articles.
     */
    public function index(Request $request)
    {
        $query = KnowledgeArticle::with('category');

        if ($request->filled('category')) {
            $query->where('waste_category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = WasteCategory::orderBy('name')->get();

        return view('admin.knowledge_articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new knowledge article.
     */
    public function create()
    {
        $categories = WasteCategory::orderBy('name')->get();
        return view('admin.knowledge_articles.create', compact('categories'));
    }

    /**
     * Store a newly created knowledge article in storage.
     */
    public function store(Request $request)
    {
        if (!$request->has('category') && $request->has('waste_category_id')) {
            $request->merge(['category' => $request->input('waste_category_id')]);
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|exists:waste_categories,id',
            'summary'           => 'nullable|string',
            'content'           => 'required|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at'      => 'nullable|date',
            'is_published'      => 'nullable|boolean',
        ]);

        $categoryId = $validated['category'];
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
            $destinationPath = public_path('uploads/knowledge_articles');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/knowledge_articles/' . $filename;
        }

        $slug = $this->generateUniqueSlug($validated['title']);

        KnowledgeArticle::create([
            'title'             => $validated['title'],
            'slug'              => $slug,
            'waste_category_id' => $categoryId,
            'summary'           => $validated['summary'] ?? null,
            'content'           => $validated['content'],
            'image'             => $imagePath,
            'published_at'      => $publishedAt,
            'is_published'      => $isPublished,
        ]);

        return redirect()->route('admin.knowledge-articles.index')
            ->with('success', 'Knowledge article created successfully.');
    }

    /**
     * Show the form for editing the specified knowledge article.
     */
    public function edit(KnowledgeArticle $knowledgeArticle)
    {
        $categories = WasteCategory::orderBy('name')->get();
        return view('admin.knowledge_articles.edit', [
            'article'    => $knowledgeArticle,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified knowledge article in storage.
     */
    public function update(Request $request, KnowledgeArticle $knowledgeArticle)
    {
        if (!$request->has('category') && $request->has('waste_category_id')) {
            $request->merge(['category' => $request->input('waste_category_id')]);
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|exists:waste_categories,id',
            'summary'           => 'nullable|string',
            'content'           => 'required|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at'      => 'nullable|date',
            'is_published'      => 'nullable|boolean',
        ]);

        $categoryId = $validated['category'];
        $isPublished = $request->has('is_published') ? (bool) $request->input('is_published') : false;
        $publishedAt = $validated['published_at'] ?? $knowledgeArticle->published_at;
        if ($isPublished && !$publishedAt) {
            $publishedAt = now();
        }

        $imagePath = $knowledgeArticle->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($knowledgeArticle->image && file_exists(public_path($knowledgeArticle->image))) {
                @unlink(public_path($knowledgeArticle->image));
            }

            $file = $request->file('image');
            $extension = $file->extension();
            $filename = Str::random(30) . '.' . $extension;
            $destinationPath = public_path('uploads/knowledge_articles');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/knowledge_articles/' . $filename;
        }

        $slug = $knowledgeArticle->slug;
        if ($knowledgeArticle->title !== $validated['title']) {
            $slug = $this->generateUniqueSlug($validated['title'], $knowledgeArticle->id);
        }

        $knowledgeArticle->update([
            'title'             => $validated['title'],
            'slug'              => $slug,
            'waste_category_id' => $categoryId,
            'summary'           => $validated['summary'] ?? null,
            'content'           => $validated['content'],
            'image'             => $imagePath,
            'published_at'      => $publishedAt,
            'is_published'      => $isPublished,
        ]);

        return redirect()->route('admin.knowledge-articles.index')
            ->with('success', 'Knowledge article updated successfully.');
    }

    /**
     * Remove the specified knowledge article from storage.
     */
    public function destroy(KnowledgeArticle $knowledgeArticle)
    {
        if ($knowledgeArticle->image && file_exists(public_path($knowledgeArticle->image))) {
            @unlink(public_path($knowledgeArticle->image));
        }

        $knowledgeArticle->delete();

        return redirect()->route('admin.knowledge-articles.index')
            ->with('success', 'Knowledge article deleted successfully.');
    }

    /**
     * Toggle published status of knowledge article.
     */
    public function togglePublished(KnowledgeArticle $knowledgeArticle)
    {
        $newPublishedState = !$knowledgeArticle->is_published;
        $publishedAt = $knowledgeArticle->published_at;

        if ($newPublishedState && !$publishedAt) {
            $publishedAt = now();
        }

        $knowledgeArticle->update([
            'is_published' => $newPublishedState,
            'published_at' => $publishedAt,
        ]);

        $statusLabel = $newPublishedState ? 'published' : 'unpublished';
        return redirect()->back()
            ->with('success', "Knowledge article '{$knowledgeArticle->title}' has been {$statusLabel}.");
    }

    /**
     * Generate unique slug for title.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        if (empty($slug)) {
            $slug = 'knowledge-article';
        }
        $originalSlug = $slug;
        $count = 1;

        while (KnowledgeArticle::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
