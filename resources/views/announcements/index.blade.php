@extends('layouts.app')

@section('title', 'Announcements & Updates – WasteWise')

@section('content')
<section class="section" style="padding-block:3rem 5rem;">
    <div class="container">
        
        <!-- Hero Header -->
        <div style="background:linear-gradient(135deg, #065f46 0%, #16a34a 100%);color:#fff;border-radius:var(--radius-xl);padding:3rem 2rem;text-align:center;margin-bottom:3rem;box-shadow:var(--shadow-lg);">
            <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.2);backdrop-filter:blur(10px);padding:.4rem 1rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;margin-bottom:1rem;color:#dcfce7;">
                📢 Community Bulletins & Eco News
            </div>
            <h1 style="font-family:'Outfit',sans-serif;font-size:2.5rem;font-weight:800;line-height:1.2;margin-bottom:.75rem;">
                Announcements & Updates
            </h1>
            <p style="color:#dcfce7;font-size:1.1rem;max-width:640px;margin:0 auto;">
                Stay informed with the latest recycling initiatives, community drop-off drives, and sustainable waste management guidelines.
            </p>
        </div>

        <!-- Announcements Grid -->
        @if($announcements->isEmpty())
            <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:4rem 2rem;text-align:center;box-shadow:var(--shadow-sm);">
                <div style="font-size:3.5rem;margin-bottom:1rem;">📢</div>
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                    No Announcements Yet
                </h3>
                <p style="color:var(--text-muted);font-size:.95rem;max-width:400px;margin:0 auto;">
                    We don't have any published updates at the moment. Please check back later for new community bulletins!
                </p>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));gap:2rem;margin-bottom:3rem;">
                @foreach($announcements as $announcement)
                    <article style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;transition:var(--transition);" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';">
                        @if($announcement->image)
                            <div style="height:180px;overflow:hidden;background:#f1f5f9;">
                                <img src="{{ asset($announcement->image) }}" alt="{{ $announcement->title }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        @else
                            <div style="height:120px;background:linear-gradient(135deg, #e6f4ea 0%, #d1fae5 100%);display:flex;align-items:center;justify-content:center;font-size:3rem;">
                                📢
                            </div>
                        @endif

                        <div style="padding:1.5rem;display:flex;flex-direction:column;flex-grow:1;">
                            <div style="font-size:.85rem;color:var(--primary-dark);font-weight:600;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem;">
                                🗓️ {{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : $announcement->created_at->format('M d, Y') }}
                            </div>

                            <h2 style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:700;color:var(--text-dark);line-height:1.35;margin-bottom:.75rem;">
                                <a href="{{ route('announcements.show', $announcement->slug) }}" style="color:inherit;text-decoration:none;">
                                    {{ $announcement->title }}
                                </a>
                            </h2>

                            <p style="color:var(--text-muted);font-size:.92rem;line-height:1.5;margin-bottom:1.5rem;flex-grow:1;">
                                {{ Str::limit(strip_tags($announcement->content), 120) }}
                            </p>

                            <div>
                                <a href="{{ route('announcements.show', $announcement->slug) }}" style="display:inline-flex;align-items:center;gap:.4rem;color:var(--primary-dark);font-weight:700;font-size:.9rem;text-decoration:none;">
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
