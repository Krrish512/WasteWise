 @extends('layouts.app')

@section('title', $tip->title . ' – Sustainability Tips')

@section('content')
<section class="section" style="padding-block:3rem 5rem;">
    <div class="container" style="max-width:850px;">
        
        <!-- Back button -->
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('sustainability-tips.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:var(--primary-dark);font-weight:600;font-size:.95rem;text-decoration:none;">
                ← Back to Sustainability Tips
            </a>
        </div>

        <!-- Article Card -->
        <article style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-md);padding:2.5rem;margin-bottom:3rem;">
            
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;margin-bottom:1rem;">
                <span style="background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;padding:.3rem .9rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:700;">
                    🏷️ {{ $tip->category }}
                </span>
                <span style="color:var(--text-muted);font-size:.85rem;">•</span>
                <span style="color:var(--text-muted);font-size:.88rem;font-weight:500;">
                    🗓️ Published {{ $tip->published_at ? $tip->published_at->format('F d, Y') : $tip->created_at->format('F d, Y') }}
                </span>
            </div>

            <h1 style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:800;color:var(--text-dark);line-height:1.25;margin-bottom:1.5rem;">
                {{ $tip->title }}
            </h1>

            @if($tip->summary)
                <div style="background:#f0fdf4;border-left:4px solid var(--primary);padding:1rem 1.25rem;border-radius:var(--radius-md);margin-bottom:2rem;font-size:1.05rem;color:#166534;line-height:1.6;font-weight:500;">
                    💡 <strong>Summary:</strong> {{ $tip->summary }}
                </div>
            @endif

            @if($tip->image)
                <div style="margin-bottom:2rem;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--border-light);max-height:480px;">
                    <img src="{{ asset($tip->image) }}" alt="{{ $tip->title }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
            @endif

            <div style="font-size:1.08rem;line-height:1.8;color:var(--text-mid);margin-bottom:2.5rem;white-space:pre-line;">
                {{ $tip->content }}
            </div>

            <div style="border-top:1px solid var(--border-light);padding-top:1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <a href="{{ route('sustainability-tips.index') }}" style="background:var(--primary-pale);color:var(--primary-dark);padding:.6rem 1.3rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                    ← Explore All Tips
                </a>

                <a href="{{ route('sustainability-tips.index', ['category' => $tip->category]) }}" style="background:#eff6ff;color:#1e40af;padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:600;font-size:.85rem;text-decoration:none;">
                    More in {{ $tip->category }} →
                </a>
            </div>

        </article>

        <!-- Related Tips in Category -->
        @if(isset($relatedTips) && $relatedTips->count() > 0)
            <div style="margin-top:3rem;">
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:var(--text-dark);margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;">
                    🌱 Related Sustainability Tips
                </h3>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:1.5rem;">
                    @foreach($relatedTips as $related)
                        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;justify-content:space-between;">
                            <div>
                                <span style="font-size:.75rem;font-weight:700;color:var(--primary-dark);background:#ecfdf5;padding:.2rem .6rem;border-radius:var(--radius-full);display:inline-block;margin-bottom:.5rem;">
                                    {{ $related->category }}
                                </span>
                                <h4 style="font-family:'Outfit',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text-dark);line-height:1.3;margin-bottom:.5rem;">
                                    <a href="{{ route('sustainability-tips.show', $related->slug) }}" style="color:inherit;text-decoration:none;">
                                        {{ $related->title }}
                                    </a>
                                </h4>
                            </div>
                            <a href="{{ route('sustainability-tips.show', $related->slug) }}" style="color:var(--primary-dark);font-size:.85rem;font-weight:700;text-decoration:none;margin-top:.75rem;display:inline-block;">
                                Read Tip →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</section>
@endsection
