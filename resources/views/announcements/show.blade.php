@extends('layouts.app')

@section('title', $announcement->title . ' – WasteWise')

@section('content')
<section class="section" style="padding-block:3rem 5rem;">
    <div class="container" style="max-width:800px;">
        
        <!-- Back button -->
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('announcements.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:var(--primary-dark);font-weight:600;font-size:.95rem;text-decoration:none;">
                ← Back to Announcements & Updates
            </a>
        </div>

        <!-- Article Card -->
        <article style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-md);padding:2.5rem;">
            
            <div style="font-size:.9rem;color:var(--primary-dark);font-weight:600;margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
                <span>📢 Community Announcement</span>
                <span>•</span>
                <span>🗓️ {{ $announcement->published_at ? $announcement->published_at->format('F d, Y') : $announcement->created_at->format('F d, Y') }}</span>
            </div>

            <h1 style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:800;color:var(--text-dark);line-height:1.25;margin-bottom:1.5rem;">
                {{ $announcement->title }}
            </h1>

            @if($announcement->image)
                <div style="margin-bottom:2rem;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--border-light);max-height:450px;">
                    <img src="{{ asset($announcement->image) }}" alt="{{ $announcement->title }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
            @endif

            <div style="font-size:1.05rem;line-height:1.75;color:var(--text-mid);margin-bottom:2.5rem;white-space:pre-line;">
                {{ $announcement->content }}
            </div>

            <div style="border-top:1px solid var(--border-light);padding-top:1.5rem;display:flex;justify-content:space-between;align-items:center;">
                <a href="{{ route('announcements.index') }}" style="background:var(--primary-pale);color:var(--primary-dark);padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                    ← Back to Updates
                </a>
            </div>

        </article>

    </div>
</section>
@endsection
