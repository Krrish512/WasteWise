@extends('layouts.app')

@section('title', '🌱 Sustainability Tips & Eco Guide – WasteWise')

@section('content')
<section class="section">
    <div class="container">
        
        <!-- Hero Header Banner -->
        <div class="hero-compact">
            <div class="hero-compact-badge">
                🌱 Sustainable Living & Eco Advice
            </div>
            <h1 class="hero-compact-title">
                Sustainability Tips & Practical Eco Guides
            </h1>
            <p class="hero-compact-subtitle" style="margin-bottom: 1.15rem;">
                Empower your journey toward zero waste with actionable tips on composting, reducing plastic consumption, conserving energy, and smart waste management.
            </p>
            
            <!-- Quick Search Bar -->
            <form action="{{ route('sustainability-tips.index') }}" method="GET" style="max-width:500px;margin:0 auto;display:flex;gap:.4rem;background:#fff;padding:.3rem;border-radius:var(--radius-full);box-shadow:0 6px 20px rgba(0,0,0,0.12);">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search eco tips, composting, zero waste..." style="flex:1;border:none;padding:.45rem drop-inline;font-size:14px;border-radius:var(--radius-full);outline:none;color:var(--text-dark);">
                <button type="submit" style="background:var(--primary-dark);color:#fff;border:none;padding:.45rem 1.15rem;border-radius:var(--radius-full);font-weight:700;font-size:13.5px;cursor:pointer;transition:var(--transition);">
                    Search
                </button>
            </form>
        </div>

        <!-- Category Filter Tabs -->
        @if(!empty($categories) && count($categories) > 0)
            <div style="display:flex;flex-wrap:wrap;gap:.5rem;justify-content:center;margin-bottom:1.75rem;">
                <a href="{{ route('sustainability-tips.index', request()->only('search')) }}" 
                   style="padding:.5rem 1.1rem;border-radius:var(--radius-full);font-size:.9rem;font-weight:600;text-decoration:none;transition:var(--transition);{{ !request('category') ? 'background:var(--primary);color:#fff;box-shadow:var(--shadow-sm);' : 'background:var(--bg-white);color:var(--text-dark);border:1px solid var(--border);' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('sustainability-tips.index', array_merge(request()->only('search'), ['category' => $cat])) }}" 
                       style="padding:.5rem 1.1rem;border-radius:var(--radius-full);font-size:.9rem;font-weight:600;text-decoration:none;transition:var(--transition);{{ request('category') === $cat ? 'background:var(--primary);color:#fff;box-shadow:var(--shadow-sm);' : 'background:var(--bg-white);color:var(--text-dark);border:1px solid var(--border);' }}">
                        🏷️ {{ $cat }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Active Filter Indicator -->
        @if(request('search') || request('category'))
            <div style="display:flex;align-items:center;justify-content:space-between;background:var(--bg-white);border:1px solid var(--border);padding:.75rem 1.25rem;border-radius:var(--radius-lg);margin-bottom:2rem;">
                <div style="font-size:.9rem;color:var(--text-dark);">
                    Showing results for 
                    @if(request('category')) <strong>Category: "{{ request('category') }}"</strong> @endif
                    @if(request('search')) <strong>Search: "{{ request('search') }}"</strong> @endif
                </div>
                <a href="{{ route('sustainability-tips.index') }}" style="color:#dc2626;font-size:.85rem;font-weight:600;text-decoration:none;">Clear Filters ✕</a>
            </div>
        @endif

        <!-- Tip Cards Grid -->
        @if($tips->isEmpty())
            <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:4rem 2rem;text-align:center;box-shadow:var(--shadow-sm);">
                <div style="font-size:3.5rem;margin-bottom:1rem;">🌱</div>
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                    No Sustainability Tips Found
                </h3>
                <p style="color:var(--text-muted);font-size:.95rem;max-width:420px;margin:0 auto 1.5rem;">
                    We couldn't find any published tips matching your search criteria. Try clearing filters or exploring other categories!
                </p>
                <a href="{{ route('sustainability-tips.index') }}" style="background:var(--primary);color:#fff;padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;display:inline-block;">
                    View All Tips
                </a>
            </div>
        @else
            <div class="announcements-grid">
                @foreach($tips as $tip)
                    <article class="announcement-card">
                        
                        @if($tip->image)
                            <div class="announcement-img-wrap" style="height:120px;position:relative;">
                                <img src="{{ asset($tip->image) }}" alt="{{ $tip->title }}">
                                <div style="position:absolute;top:.6rem;left:.6rem;background:rgba(255,255,255,0.94);backdrop-filter:blur(6px);color:var(--primary-dark);padding:.2rem .65rem;border-radius:var(--radius-full);font-size:.75rem;font-weight:700;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                                    🏷️ {{ $tip->category }}
                                </div>
                            </div>
                        @else
                            <div class="announcement-icon-placeholder" style="height:95px;position:relative;">
                                <div style="font-size:2.2rem;">🌱</div>
                                <div style="position:absolute;top:.6rem;left:.6rem;background:#fff;color:var(--primary-dark);padding:.2rem .65rem;border-radius:var(--radius-full);font-size:.75rem;font-weight:700;box-shadow:0 2px 6px rgba(0,0,0,0.06);border:1px solid var(--border-light);">
                                    🏷️ {{ $tip->category }}
                                </div>
                            </div>
                        @endif

                        <div class="announcement-body">
                            <div class="announcement-date">
                                🗓️ {{ $tip->published_at ? $tip->published_at->format('M d, Y') : $tip->created_at->format('M d, Y') }}
                            </div>

                            <h2 class="announcement-title">
                                <a href="{{ route('sustainability-tips.show', $tip->slug) }}">
                                    {{ $tip->title }}
                                </a>
                            </h2>

                            <p class="announcement-desc">
                                @if($tip->summary)
                                    {{ $tip->summary }}
                                @else
                                    {{ Str::limit(strip_tags($tip->content), 105) }}
                                @endif
                            </p>

                            <div>
                                <a href="{{ route('sustainability-tips.show', $tip->slug) }}" class="announcement-link">
                                    Read Tip →
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($tips->hasPages())
                <div style="display:flex;justify-content:center;margin-top:2rem;">
                    {{ $tips->links() }}
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
