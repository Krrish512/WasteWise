<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use App\Models\WasteItem;
use Illuminate\Http\Request;

class WasteSearchController extends Controller
{
    /**
     * GET /api/waste/search?q=mobile+phone
     *
     * Enhanced database-driven search for waste items matching exact names,
     * case-insensitive names, keywords, and normalized token variations.
     */
    public function search(Request $request)
    {
        // ── 1. Validate query parameter ──────────────────────────────────────
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = trim($validated['q']);

        // ── 2. Find best matching WasteItem using multi-tier database search ─
        $item = $this->findBestMatch($query);

        // ── 3. Handle genuinely unknown items (404 with suggestions) ────────
        if (!$item) {
            // Retrieve top existing waste items as helpful suggestions
            $suggestions = WasteItem::with('category')
                ->take(4)
                ->get()
                ->map(function ($s) {
                    return [
                        'id'       => $s->id,
                        'name'     => $s->name,
                        'category' => $s->category ? $s->category->name : null,
                    ];
                });

            return response()->json([
                'success'     => false,
                'message'     => 'Sorry, we couldn\'t find a matching waste item for "' . $query . '". '
                    . 'Try searching for common items such as Old Laptop, Old Mobile, Plastic Bottle, or Newspaper.',
                'suggestions' => $suggestions,
                'admin_note'  => 'If this item should be supported, a WasteWise administrator can add it via the Admin Waste Items management portal.',
            ], 404);
        }

        // ── 4. Record search history for authenticated users ──────────────────
        if (auth()->check()) {
            SearchHistory::create([
                'user_id'       => auth()->id(),
                'waste_item_id' => $item->id,
                'searched_at'   => now(),
            ]);
        }

        // ── 5. Fetch collection centres handling this waste category ─────────
        $centres = collect();
        $safetyWarning = null;

        $itemNameLower = strtolower($item->name);
        $itemKeywords = strtolower($item->keywords ?? '');
        $isLeadAcid = str_contains($itemNameLower, 'lead') || str_contains($itemKeywords, 'lead acid');
        $isLithiumIon = str_contains($itemNameLower, 'lithium') || str_contains($itemKeywords, 'lithium');

        if ($isLithiumIon) {
            $safetyWarning = 'Never crush, puncture, or send Lithium-Ion batteries to Lead-Acid scrap dealers—fire and toxic gas hazard; use authorized e-waste / Li-ion take-back channels.';
        }

        if ($item->category) {
            $includeUnverified = $request->boolean('include_unverified');

            // Load collection centres (via category and pivot)
            $centresQuery = \App\Models\CollectionCentre::with(['city', 'categories', 'category'])
                ->where(function ($q) use ($item) {
                    $q->where('category_id', $item->category_id)
                      ->orWhereHas('categories', function ($cq) use ($item) {
                          $cq->where('waste_categories.id', $item->category_id);
                      });
                });

            // Filter unverified / expired unless explicit citizen toggle is checked
            if (!$includeUnverified) {
                $centresQuery->where(function ($q) {
                    $q->whereNull('status')
                      ->orWhere('status', 'active');
                })->where(function ($q) {
                    $q->whereNull('authorization_status')
                      ->orWhere('authorization_status', '!=', 'expired');
                })->where(function ($q) {
                    $q->whereNull('authorization_valid_until')
                      ->orWhere('authorization_valid_until', '>=', now()->toDateString());
                });
            }

            $rawCentres = $centresQuery->get();

            // Strict battery chemistry filtering:
            if ($isLeadAcid) {
                // Only return centres handling lead-acid / smelters / specialized battery recovery
                $rawCentres = $rawCentres->filter(function ($c) {
                    $text = strtolower($c->name . ' ' . ($c->notes ?? '') . ' ' . ($c->facility_type ?? ''));
                    return str_contains($text, 'lead') || str_contains($text, 'battery') || str_contains($text, 'smelter') || in_array($c->facility_type, ['authorized_recycler', 'processing_facility', 'industrial_facility']);
                });
            } elseif ($isLithiumIon) {
                // Strictly exclude lead-acid smelters/dealers; keep e-waste and take-back facilities
                $rawCentres = $rawCentres->filter(function ($c) {
                    $text = strtolower($c->name . ' ' . ($c->notes ?? ''));
                    return !str_contains($text, 'lead-acid') && !str_contains($text, 'lead smelter');
                });
            }

            $centres = $rawCentres->map(function ($centre) {
                return [
                    'id'                        => $centre->id,
                    'name'                      => $centre->name,
                    'city'                      => $centre->city ? $centre->city->name : null,
                    'district'                  => $centre->district ?? ($centre->city ? $centre->city->district : null),
                    'address'                   => $centre->address,
                    'phone'                     => $centre->phone,
                    'latitude'                  => $centre->latitude !== null ? (float) $centre->latitude : null,
                    'longitude'                 => $centre->longitude !== null ? (float) $centre->longitude : null,
                    'facility_type'             => $centre->facility_type ?? 'public_dropoff',
                    'public_access'             => $centre->public_access ?? 'public_dropoff',
                    'authorization_status'      => $centre->authorization_status ?? 'verified',
                    'authorization_id'          => $centre->authorization_id,
                    'authorization_valid_until' => $centre->authorization_valid_until ? $centre->authorization_valid_until->format('Y-m-d') : null,
                    'verification_date'         => $centre->verification_date ? $centre->verification_date->format('Y-m-d') : null,
                    'confidence'                => $centre->confidence ?? 'medium',
                    'status'                    => $centre->status ?? 'active',
                    'website'                   => $centre->website,
                    'source_url'                => $centre->source_url,
                    'notes'                     => $centre->notes,
                    'categories'                => $centre->categories ? $centre->categories->pluck('name')->toArray() : [],
                ];
            })->values();
        }

        // ── 6. Return standard success JSON response ──────────────────────────
        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $item->id,
                'name'            => $item->name,
                'description'     => $item->description,
                'safety_warning'  => $safetyWarning,
                'category'        => $item->category ? [
                    'id'   => $item->category->id,
                    'name' => $item->category->name,
                ] : null,
                'disposal_method' => $item->disposalMethod ? [
                    'id'           => $item->disposalMethod->id,
                    'name'         => $item->disposalMethod->name,
                    'instructions' => $item->disposalMethod->instructions,
                    'avoid'        => $item->disposalMethod->avoid,
                ] : null,
                'collection_centres' => $centres,
            ],
        ]);
    }

    /**
     * Multi-tiered database-driven search algorithm.
     * Matches exact names, normalized names, keyword fields, phrases, and token stems.
     */
    protected function findBestMatch(string $rawQuery): ?WasteItem
    {
        $all = WasteItem::with(['category', 'disposalMethod'])->get();
        if ($all->isEmpty()) {
            return null;
        }

        $rawLower   = strtolower(trim($rawQuery));
        $normalized = $this->normalizeText($rawQuery);
        $stemmed    = $this->stemText($normalized);

        // ── Tier 1: Exact Name Match (case-insensitive) ──────────────────────
        foreach ($all as $item) {
            if (strcasecmp($item->name, trim($rawQuery)) === 0) {
                return $item;
            }
        }

        // ── Tier 2: Normalized & Stemmed Exact Name Match ────────────────────
        foreach ($all as $item) {
            $itemNameNorm = $this->normalizeText($item->name);
            $itemNameStem = $this->stemText($itemNameNorm);

            if ($itemNameNorm === $normalized || $itemNameStem === $stemmed) {
                return $item;
            }
        }

        // ── Tier 3: Direct Keyword Match ─────────────────────────────────────
        foreach ($all as $item) {
            $keywords = $this->extractKeywords($item);
            foreach ($keywords as $kw) {
                $kwNorm = $this->normalizeText($kw);
                $kwStem = $this->stemText($kwNorm);
                if ($kwNorm === $normalized || $kwStem === $stemmed) {
                    return $item;
                }
            }
        }

        // ── Tier 4: Phrase & Substring Match ──────────────────────────────────
        foreach ($all as $item) {
            $itemNameNorm = $this->normalizeText($item->name);
            $itemNameStem = $this->stemText($itemNameNorm);

            if (!empty($normalized) && (str_contains($itemNameNorm, $normalized) || str_contains($normalized, $itemNameNorm))) {
                return $item;
            }

            if (!empty($stemmed) && (str_contains($itemNameStem, $stemmed) || str_contains($stemmed, $itemNameStem))) {
                return $item;
            }

            $keywords = $this->extractKeywords($item);
            foreach ($keywords as $kw) {
                $kwNorm = $this->normalizeText($kw);
                $kwStem = $this->stemText($kwNorm);
                if (!empty($kwNorm) && (str_contains($kwNorm, $normalized) || str_contains($normalized, $kwNorm) || str_contains($kwStem, $stemmed))) {
                    return $item;
                }
            }
        }

        // ── Tier 5: Meaningful Token Matching (Removing noise/modifier words) ─
        $meaningfulTokens = $this->extractMeaningfulTokens($rawQuery);
        if (!empty($meaningfulTokens)) {
            $cleanPhrase = implode(' ', $meaningfulTokens);
            $cleanStem   = $this->stemText($cleanPhrase);

            foreach ($all as $item) {
                $itemNameNorm = $this->normalizeText($item->name);
                $itemNameStem = $this->stemText($itemNameNorm);

                if (str_contains($itemNameNorm, $cleanPhrase) || str_contains($itemNameStem, $cleanStem)) {
                    return $item;
                }

                $keywords = $this->extractKeywords($item);
                foreach ($keywords as $kw) {
                    $kwNorm = $this->normalizeText($kw);
                    $kwStem = $this->stemText($kwNorm);
                    if (str_contains($kwNorm, $cleanPhrase) || str_contains($kwStem, $cleanStem)) {
                        return $item;
                    }
                    foreach ($meaningfulTokens as $token) {
                        $tokenStem = $this->stemText($token);
                        if ($kwNorm === $token || $kwStem === $tokenStem || (strlen($token) >= 4 && str_contains($kwNorm, $token))) {
                            return $item;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Normalizes text by lowercasing, converting hyphens/slashes to spaces, and removing punctuation.
     */
    protected function normalizeText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[\-_\/\\\\]+/', ' ', $text);
        $text = preg_replace('/[^\w\s]/u', '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Basic stemming helper for common English plurals (s, es, ies).
     */
    protected function stemText(string $text): string
    {
        $words = explode(' ', $text);
        $stemmed = array_map(function ($word) {
            if (str_ends_with($word, 'ies') && strlen($word) > 4) {
                return substr($word, 0, -3) . 'y';
            }
            if (str_ends_with($word, 'es') && strlen($word) > 3 && !in_array($word, ['clothes', 'glasses'])) {
                return substr($word, 0, -2);
            }
            if (str_ends_with($word, 's') && !str_ends_with($word, 'ss') && strlen($word) > 3 && $word !== 'news') {
                return substr($word, 0, -1);
            }
            return $word;
        }, $words);

        return implode(' ', $stemmed);
    }

    /**
     * Extracts meaningful search tokens by filtering out common noise words.
     */
    protected function extractMeaningfulTokens(string $rawQuery): array
    {
        $normalized = $this->normalizeText($rawQuery);
        $words = explode(' ', $normalized);

        $noiseWords = [
            'old', 'broken', 'used', 'damaged', 'waste', 'trash', 'garbage',
            'dispose', 'disposal', 'how', 'to', 'the', 'a', 'an', 'my', 'of',
            'for', 'and', 'junk', 'unwanted', 'expired', 'empty', 'item', 'items'
        ];

        $meaningful = array_filter($words, function ($w) use ($noiseWords) {
            return strlen($w) >= 2 && !in_array($w, $noiseWords);
        });

        return array_values($meaningful);
    }

    /**
     * Extracts keyword tokens from a WasteItem record.
     */
    protected function extractKeywords(WasteItem $item): array
    {
        if (empty($item->keywords)) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', strtolower($item->keywords))));
    }
}
