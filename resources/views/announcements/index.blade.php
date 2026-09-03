@extends('layouts.app')

@section('title', 'Announcements & Updates – WasteWise')

@section('content')
<section class="section" style="padding-block:2rem 3rem;">
    <div class="container">
        
        <!-- Hero Header -->
        <div style="background:linear-gradient(135deg, #065f46 0%, #16a34a 100%);color:#fff;border-radius:var(--radius-xl);padding:2.5rem 2rem;text-align:center;margin-bottom:2rem;box-shadow:var(--shadow-md);max-width:1200px;margin-inline:auto;">
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.2);backdrop-filter:blur(10px);padding:.3rem .85rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:600;margin-bottom:.75rem;color:#dcfce7;">
                📢 Community Bulletins & Eco News
            </div>
            <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(2rem, 3.2vw, 2.35rem);font-weight:800;line-height:1.2;margin-bottom:.5rem;">
                Announcements & Updates
            </h1>
            <p style="color:#dcfce7;font-size:1rem;max-width:600px;margin:0 auto;line-height:1.55;">
                Stay informed with the latest recycling initiatives, community drop-off drives, and sustainable waste management guidelines.
            </p>
        </div>

        <!-- Announcements Grid -->
        @if($announcements->isEmpty())
            <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:3rem 1.5rem;text-align:center;box-shadow:var(--shadow-sm);">
                <div style="font-size:2.5rem;margin-bottom:.75rem;">📢</div>
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;">
                    No Announcements Yet
                </h3>
                <p style="color:var(--text-muted);font-size:.9rem;max-width:380px;margin:0 auto;">
                    We don't have any published updates at the moment. Please check back later for new community bulletins!
                </p>
            </div>
        @else
            <div class="announcements-grid">
                @foreach($announcements as $announcement)
                    <article class="announcement-card">
                        @if($announcement->image)
                            <div class="announcement-img-wrap">
                                <img src="{{ asset($announcement->image) }}" alt="{{ $announcement->title }}">
                            </div>
                        @else
                            <div class="announcement-icon-placeholder">
                                📢
                            </div>
                        @endif

                        <div class="announcement-body">
                            <div class="announcement-date">
                                🗓️ {{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : $announcement->created_at->format('M d, Y') }}
                            </div>

                            <h2 class="announcement-title">
                                <a href="{{ route('announcements.show', $announcement->slug) }}">
                                    {{ $announcement->title }}
                                </a>
                            </h2>

                            <p class="announcement-desc">
                                {{ Str::limit(strip_tags($announcement->content), 110) }}
                            </p>

                            <div>
                                <a href="{{ route('announcements.show', $announcement->slug) }}" class="announcement-link">
                                    Read Full Update →
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($announcements->hasPages())
                <div style="display:flex;justify-content:center;margin-top:2rem;">
                    {{ $announcements->links() }}
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
