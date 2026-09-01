<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use App\Models\WasteCategory;
use Illuminate\Http\Request;

class KnowledgeArticleController extends Controller
{
    /**
     * Display a listing of published knowledge articles.
     */
    public function index(Request $request)
    {
        $query = KnowledgeArticle::published()->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $cat = $request->category;
            $query->where(function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('waste_category_id', $cat);
                } else {
                    $q->whereHas('category', function ($cq) use ($cat) {
                        $cq->where('name', $cat);
                    });
                }
            });
        }

        $articles = $query->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        $categories = WasteCategory::orderBy('name')->get();

        return view('knowledge.index', compact('articles', 'categories'));
    }

    /**
     * Display the specified published knowledge article detail.
     */
    public function show(string $slug)
    {
        $article = KnowledgeArticle::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('knowledge.show', compact('article'));
    }
}
