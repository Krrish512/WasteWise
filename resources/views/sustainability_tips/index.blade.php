@extends('layouts.app')

@section('title', '🌱 Sustainability Tips & Eco Guide – WasteWise')

@section('content')
<section class="section" style="padding-block:3rem 5rem;">
    <div class="container">
        
        <!-- Hero Header Banner -->
        <div style="background:linear-gradient(135deg, #064e3b 0%, #047857 50%, #10b981 100%);color:#fff;border-radius:var(--radius-xl);padding:3rem 2rem;text-align:center;margin-bottom:2.5rem;box-shadow:var(--shadow-lg);">
            <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.18);backdrop-filter:blur(10px);padding:.4rem 1.1rem;border-radius:var(--radius-full);font-size:.88rem;font-weight:600;margin-bottom:1rem;color:#dcfce7;">
                🌱 Sustainable Living & Eco Advice
            </div>
            <h1 style="font-family:'Outfit',sans-serif;font-size:2.5rem;font-weight:800;line-height:1.2;margin-bottom:.75rem;">
                Sustainability Tips & Practical Eco Guides
            </h1>
            <p style="color:#dcfce7;font-size:1.1rem;max-width:680px;margin:0 auto 1.5rem;">
                Empower your journey toward zero waste with actionable tips on composting, reducing plastic consumption, conserving energy, and smart waste management.
            </p>
            
            <!-- Quick Search Bar -->
            <form action="{{ route('sustainability-tips.index') }}" method="GET" style="max-width:550px;margin:0 auto;display:flex;gap:.5rem;background:#fff;padding:.4rem;border-radius:var(--radius-full);box-shadow:0 8px 25px rgba(0,0,0,0.15);">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search eco tips, composting, zero waste..." style="flex:1;border:none;padding:.6rem 1.2rem;font-size:.95rem;border-radius:var(--radius-full);outline:none;color:var(--text-dark);">
                <button type="submit" style="background:var(--primary-dark);color:#fff;border:none;padding:.6rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;cursor:pointer;transition:var(--transition);">
                    Search
                </button>
            </form>
        </div>

        <!-- Category Filter Tabs -->
        @if(!empty($categories) && count($categories) > 0)
            <div style="display:flex;flex-wrap:wrap;gap:.6rem;justify-content:center;margin-bottom:2.5rem;">
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
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));gap:2rem;margin-bottom:3rem;">
                @foreach($tips as $tip)
                    <article style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;transition:var(--transition);" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';">
                        
                        @if($tip->image)
                            <div style="height:190px;overflow:hidden;background:#f1f5f9;position:relative;">
                                <img src="{{ asset($tip->image) }}" alt="{{ $tip->title }}" style="width:100%;height:100%;object-fit:cover;">
                                <div style="position:absolute;top:1rem;left:1rem;background:rgba(255,255,255,0.92);backdrop-filter:blur(8px);color:var(--primary-dark);padding:.25rem .75rem;border-radius:var(--radius-full);font-size:.78rem;font-weight:700;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                                    🏷️ {{ $tip->category }}
                                </div>
                            </div>
                        @else
                            <div style="height:140px;background:linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);display:flex;align-items:center;justify-content:center;position:relative;border-bottom:1px solid var(--border-light);">
                                <div style="font-size:3.5rem;">🌱</div>
                                <div style="position:absolute;top:1rem;left:1rem;background:#fff;color:var(--primary-dark);padding:.25rem .75rem;border-radius:var(--radius-full);font-size:.78rem;font-weight:700;box-shadow:0 2px 8px rgba(0,0,0,0.06);border:1px solid var(--border-light);">
                                    🏷️ {{ $tip->category }}
                                </div>
                            </div>
                        @endif

                        <div style="padding:1.5rem;display:flex;flex-direction:column;flex-grow:1;">
                            <div style="font-size:.82rem;color:var(--text-muted);font-weight:500;margin-bottom:.6rem;display:flex;align-items:center;gap:.4rem;">
                                🗓️ {{ $tip->published_at ? $tip->published_at->format('M d, Y') : $tip->created_at->format('M d, Y') }}
                            </div>

                            <h2 style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:700;color:var(--text-dark);line-height:1.35;margin-bottom:.75rem;">
                                <a href="{{ route('sustainability-tips.show', $tip->slug) }}" style="color:inherit;text-decoration:none;">
                                    {{ $tip->title }}
                                </a>
                            </h2>

                            <p style="color:var(--text-muted);font-size:.92rem;line-height:1.5;margin-bottom:1.5rem;flex-grow:1;">
                                @if($tip->summary)
                                    {{ $tip->summary }}
                                @else
                                    {{ Str::limit(strip_tags($tip->content), 120) }}
                                @endif
                            </p>

                            <div>
                                <a href="{{ route('sustainability-tips.show', $tip->slug) }}" style="display:inline-flex;align-items:center;gap:.4rem;color:var(--primary-dark);font-weight:700;font-size:.9rem;text-decoration:none;">
                                    Read Sustainability Tip →
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
