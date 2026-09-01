@extends('layouts.app')

@section('title', '📚 Waste Knowledge Hub – WasteWise')

@section('content')
<!-- Hero Section -->
<section style="background:linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%);color:#fff;padding-block:3.5rem 3rem;position:relative;overflow:hidden;">
    <div class="container" style="position:relative;z-index:2;text-align:center;max-width:850px;">
        <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);padding:.4rem 1.1rem;border-radius:var(--radius-full);font-size:.88rem;font-weight:700;margin-bottom:1.2rem;color:#dcfce7;">
            📚 Educational Resource
        </div>
        <h1 style="font-family:'Outfit',sans-serif;font-size:2.8rem;font-weight:900;line-height:1.25;margin-bottom:1rem;letter-spacing:-0.02em;">
            📚 Waste Knowledge Hub
        </h1>
        <p style="color:#dcfce7;font-size:1.15rem;max-width:680px;margin:0 auto 2.5rem;line-height:1.6;font-weight:400;">
            Learn how different types of waste should be handled, recycled, and disposed responsibly.
        </p>

        <!-- Search Box -->
        <form action="{{ route('knowledge.index') }}" method="GET" style="max-width:620px;margin:0 auto;display:flex;gap:.5rem;background:#fff;padding:.4rem;border-radius:var(--radius-full);box-shadow:0 12px 30px rgba(0,0,0,.15);">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search waste knowledge..." style="flex:1;border:none;outline:none;padding:.75rem 1.4rem;font-size:1rem;border-radius:var(--radius-full);color:var(--text-dark);">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.75rem 1.6rem;border-radius:var(--radius-full);font-weight:700;font-size:.95rem;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;">
                🔍 Search
            </button>
        </form>
    </div>
</section>

<!-- Filter Pills & Articles Grid Section -->
<section class="section" style="padding-block:3rem 5rem;background:var(--bg-base);">
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
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:2rem;">
                @foreach($articles as $article)
                    <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-sm);transition:var(--transition);display:flex;flex-direction:column;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';">
                        
                        <!-- Image or Default Banner -->
                        <div style="height:190px;background:linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                            @if($article->image)
                                <img src="{{ asset($article->image) }}" alt="{{ $article->title }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div style="font-size:3.5rem;opacity:.8;">♻️</div>
                            @endif
                            <div style="position:absolute;top:1rem;left:1rem;">
                                <span style="background:rgba(255,255,255,.92);backdrop-filter:blur(8px);color:var(--primary-dark);padding:.3rem .85rem;border-radius:var(--radius-full);font-size:.78rem;font-weight:800;box-shadow:0 2px 8px rgba(0,0,0,.08);display:inline-flex;align-items:center;gap:.3rem;">
                                    ♻️ {{ $article->category ? $article->category->name : 'General' }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
                            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.5rem;font-weight:500;">
                                📅 {{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}
                            </div>

                            <h3 style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:800;color:var(--text-dark);margin-bottom:.75rem;line-height:1.35;">
                                <a href="{{ route('knowledge.show', $article->slug) }}" style="color:inherit;text-decoration:none;">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <p style="color:var(--text-muted);font-size:.92rem;line-height:1.55;margin-bottom:1.5rem;flex:1;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $article->summary ?? Str::limit(strip_tags($article->content), 120) }}
                            </p>

                            <!-- Read More Button -->
                            <div style="border-top:1px solid var(--border-light);padding-top:1rem;margin-top:auto;">
                                <a href="{{ route('knowledge.show', $article->slug) }}" style="display:inline-flex;align-items:center;gap:.4rem;color:var(--primary);font-weight:700;font-size:.9rem;text-decoration:none;transition:var(--transition);" onmouseover="this.style.gap='.6rem';" onmouseout="this.style.gap='.4rem';">
                                    <span>Read More</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            @if($articles->hasPages())
                <div style="margin-top:3rem;">
                    {{ $articles->links() }}
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
