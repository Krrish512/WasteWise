@extends('layouts.app')

@section('title', $article->title . ' – Waste Knowledge Hub')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:860px;">
        
        <!-- Navigation Back Link -->
        <div style="margin-bottom:2rem;">
            <a href="{{ route('knowledge.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:var(--primary-dark);font-weight:700;font-size:.92rem;text-decoration:none;background:#ecfdf5;padding:.5rem 1rem;border-radius:var(--radius-full);border:1px solid #a7f3d0;">
                ← Back to Knowledge Hub
            </a>
        </div>

        <!-- Article Card Container -->
        <article style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-md);">
            
            <!-- Cover Image if present -->
            @if($article->image)
                <div style="width:100%;max-height:420px;overflow:hidden;background:#f8fafc;">
                    <img src="{{ asset($article->image) }}" alt="{{ $article->title }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            @endif

            <div style="padding:2.5rem;">
                
                <!-- Category Badge & Published Date -->
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem;">
                    @if($article->category)
                        <a href="{{ route('knowledge.index', ['category' => $article->category->id]) }}" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;padding:.3rem .9rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                            ♻️ {{ $article->category->name }}
                        </a>
                    @endif
                    <span style="font-size:.88rem;color:var(--text-muted);font-weight:500;">
                        📅 Published on {{ $article->published_at ? $article->published_at->format('F d, Y') : $article->created_at->format('F d, Y') }}
                    </span>
                </div>

                <!-- Title -->
                <h1 style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:900;color:var(--text-dark);line-height:1.25;margin-bottom:1.25rem;">
                    {{ $article->title }}
                </h1>

                <!-- Summary Highlight Box (if available) -->
                @if($article->summary)
                    <div style="background:#f0fdf4;border-left:4px solid var(--primary);padding:1.25rem 1.5rem;border-radius:0 var(--radius-lg) var(--radius-lg) 0;margin-bottom:2rem;color:#166534;font-size:1.05rem;line-height:1.6;font-weight:500;">
                        💡 {{ $article->summary }}
                    </div>
                @endif

                <!-- Main Educational Content -->
                <div style="font-size:1.05rem;line-height:1.8;color:var(--text-dark);margin-bottom:3rem;white-space:pre-line;">
                    {!! nl2br(e($article->content)) !!}
                </div>

                <!-- WasteWise Integrations Section -->
                <div style="background:linear-gradient(135deg, #f8fafc 0%, #ecfdf5 100%);border:1px solid #a7f3d0;border-radius:var(--radius-xl);padding:2rem;margin-top:2rem;">
                    <h3 style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:800;color:var(--text-dark);margin-bottom:.5rem;display:flex;align-items:center;gap:.5rem;">
                        🌿 WasteWise Smart Disposal Integration
                    </h3>
                    <p style="color:var(--text-muted);font-size:.95rem;margin-bottom:1.5rem;">
                        Need quick disposal guidance or want to locate a certified recycling collection centre near you?
                    </p>

                    <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                        <!-- Search This Waste Button -->
                        @php
                            $searchQuery = $article->category ? $article->category->name : $article->title;
                        @endphp
                        <a href="{{ route('home') }}?q={{ urlencode($searchQuery) }}" style="background:var(--primary);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.92rem;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-sm);">
                            🔍 Search {{ $searchQuery }}
                        </a>

                        <!-- Find Collection Centre Button -->
                        <a href="{{ url('/#centres') }}" style="background:#fff;color:var(--primary-dark);border:1px solid var(--primary);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.92rem;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;">
                            📍 Find Collection Centre
                        </a>
                    </div>
                </div>

            </div>

        </article>

        <!-- Bottom Back Button -->
        <div style="margin-top:2rem;text-align:center;">
            <a href="{{ route('knowledge.index') }}" style="color:var(--text-muted);font-weight:600;font-size:.92rem;text-decoration:none;">
                ← Back to Knowledge Hub
            </a>
        </div>

    </div>
</section>
@endsection
