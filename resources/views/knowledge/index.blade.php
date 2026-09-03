@extends('layouts.app')

@section('title', '📚 Waste Knowledge Hub – WasteWise')

@section('content')
<!-- Hero Section -->
<section style="background:linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%);color:#fff;padding-block:2.5rem 2rem;position:relative;overflow:hidden;">
    <div class="container" style="position:relative;z-index:2;text-align:center;max-width:780px;">
        <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);padding:.3rem .9rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;margin-bottom:.85rem;color:#dcfce7;">
            📚 Educational Resource
        </div>
        <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(1.75rem, 3.5vw, 2.2rem);font-weight:800;line-height:1.2;margin-bottom:.6rem;letter-spacing:-0.02em;">
            📚 Waste Knowledge Hub
        </h1>
        <p style="color:#dcfce7;font-size:.95rem;max-width:600px;margin:0 auto 1.5rem;line-height:1.55;font-weight:400;">
            Learn how different types of waste should be handled, recycled, and disposed responsibly.
        </p>

        <!-- Search Box -->
        <form action="{{ route('knowledge.index') }}" method="GET" style="max-width:540px;margin:0 auto;display:flex;gap:.4rem;background:#fff;padding:.35rem;border-radius:var(--radius-full);box-shadow:0 8px 24px rgba(0,0,0,.12);">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search waste knowledge..." style="flex:1;border:none;outline:none;padding:.55rem 1.1rem;font-size:.9rem;border-radius:var(--radius-full);color:var(--text-dark);">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.55rem 1.25rem;border-radius:var(--radius-full);font-weight:700;font-size:.875rem;cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;">
                🔍 Search
            </button>
        </form>
    </div>
</section>

<!-- Filter Pills & Articles Grid Section -->
<section class="section" style="padding-block:2.25rem 3.5rem;background:var(--bg-base);">
    <div class="container">
        
        <!-- Category Filter Pills -->
        <div style="display:flex;flex-wrap:wrap;gap:.6rem;justify-content:center;margin-bottom:3rem;">
            <a href="{{ route('knowledge.index', array_filter(['search' => request('search')])) }}" 
               style="padding:.55rem 1.25rem;border-radius:var(--radius-full);font-size:.9rem;font-weight:700;text-decoration:none;transition:var(--transition);{{ !request('category') ? 'background:var(--primary);color:#fff;box-shadow:var(--shadow-sm);' : 'background:#fff;color:var(--text-dark);border:1px solid var(--border);' }}">
                🌐 All Categories
            </a>

            @foreach($categories as $cat)
                @php
                    $isSelected = (string) request('category') === (string) $cat->id || request('category') === $cat->name;
                @endphp
                <a href="{{ route('knowledge.index', array_filter(['category' => $cat->id, 'search' => request('search')])) }}" 
                   style="padding:.55rem 1.25rem;border-radius:var(--radius-full);font-size:.9rem;font-weight:700;text-decoration:none;transition:var(--transition);{{ $isSelected ? 'background:var(--primary);color:#fff;box-shadow:var(--shadow-sm);' : 'background:#fff;color:var(--text-dark);border:1px solid var(--border);' }}">
                    ♻️ {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if(request('search') || request('category'))
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;background:#fff;padding:1rem 1.5rem;border-radius:var(--radius-lg);border:1px solid var(--border-light);">
                <div style="font-size:.95rem;color:var(--text-mid);">
                    Showing results
                    @if(request('search')) for <strong>"{{ request('search') }}"</strong> @endif
                    @if(request('category')) in category <strong>
                        @php
                            $catObj = $categories->firstWhere('id', request('category')) ?? $categories->firstWhere('name', request('category'));
                        @endphp
                        {{ $catObj ? $catObj->name : request('category') }}
                    </strong> @endif
                </div>
                <a href="{{ route('knowledge.index') }}" style="color:#dc2626;font-weight:600;font-size:.88rem;text-decoration:none;">✕ Clear Filters</a>
            </div>
        @endif

        <!-- Articles Grid -->
        @if($articles->isEmpty())
            <div style="text-align:center;padding:4rem 1rem;background:#fff;border-radius:var(--radius-xl);border:1px solid var(--border-light);max-width:600px;margin:0 auto;">
                <div style="font-size:3.5rem;margin-bottom:1rem;">📚</div>
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:var(--text-dark);margin-bottom:.5rem;">No articles found</h3>
                <p style="color:var(--text-muted);font-size:.95rem;margin-bottom:1.5rem;">We couldn't find any knowledge articles matching your criteria.</p>
                <a href="{{ route('knowledge.index') }}" style="background:var(--primary);color:#fff;padding:.65rem 1.4rem;border-radius:var(--radius-full);font-weight:700;text-decoration:none;font-size:.9rem;">View All Articles</a>
            </div>
        @else
            <div class="announcements-grid">
                @foreach($articles as $article)
                    <article class="announcement-card">
                        
                        <!-- Image or Default Banner -->
                        @if($article->image)
                            <div class="announcement-img-wrap" style="height:120px;position:relative;">
                                <img src="{{ asset($article->image) }}" alt="{{ $article->title }}">
                                <div style="position:absolute;top:.6rem;left:.6rem;">
                                    <span style="background:rgba(255,255,255,.94);backdrop-filter:blur(6px);color:var(--primary-dark);padding:.2rem .65rem;border-radius:var(--radius-full);font-size:.75rem;font-weight:800;box-shadow:0 2px 6px rgba(0,0,0,.08);display:inline-flex;align-items:center;gap:.3rem;">
                                        ♻️ {{ $article->category ? $article->category->name : 'General' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="announcement-icon-placeholder" style="height:95px;position:relative;">
                                <div style="font-size:2.2rem;opacity:.85;">♻️</div>
                                <div style="position:absolute;top:.6rem;left:.6rem;">
                                    <span style="background:#fff;color:var(--primary-dark);padding:.2rem .65rem;border-radius:var(--radius-full);font-size:.75rem;font-weight:800;box-shadow:0 2px 6px rgba(0,0,0,.06);border:1px solid var(--border-light);display:inline-flex;align-items:center;gap:.3rem;">
                                        ♻️ {{ $article->category ? $article->category->name : 'General' }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Card Body -->
                        <div class="announcement-body">
                            <div class="announcement-date">
                                📅 {{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}
                            </div>

                            <h3 class="announcement-title">
                                <a href="{{ route('knowledge.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <p class="announcement-desc">
                                {{ $article->summary ?? Str::limit(strip_tags($article->content), 105) }}
                            </p>

                            <!-- Read More Button -->
                            <div>
                                <a href="{{ route('knowledge.show', $article->slug) }}" class="announcement-link">
                                    <span>Read Article →</span>
                                </a>
                            </div>
                        </div>

                    </article>
                @endforeach
            </div>

            @if($articles->hasPages())
                <div class="knowledge-pagination" style="margin-top:3rem;">
                    {{ $articles->links() }}
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
