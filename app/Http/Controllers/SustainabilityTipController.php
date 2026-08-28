<?php

namespace App\Http\Controllers;

use App\Models\SustainabilityTip;
use Illuminate\Http\Request;

class SustainabilityTipController extends Controller
{
    /**
     * Display a listing of published sustainability tips.
     */
    public function index(Request $request)
    {
        $query = SustainabilityTip::published();

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $tips = $query->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        $categories = SustainabilityTip::published()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('sustainability_tips.index', compact('tips', 'categories'));
    }

    /**
     * Display the specified published sustainability tip.
     */
    public function show(string $slug)
    {
        $tip = SustainabilityTip::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related published tips in the same category
        $relatedTips = SustainabilityTip::published()
            ->where('id', '!=', $tip->id)
            ->where('category', $tip->category)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('sustainability_tips.show', compact('tip', 'relatedTips'));
    }
}
