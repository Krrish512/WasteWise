@extends('layouts.app')

@section('title', 'User Dashboard – WasteWise')

@section('content')
<section class="section" style="padding-block:2rem 3.5rem;">
    <div class="container">
        
        <!-- Welcome Hero Banner -->
        <div style="background:linear-gradient(135deg, #065f46 0%, #16a34a 100%);color:#fff;border-radius:var(--radius-xl);padding:1.75rem 1.5rem;box-shadow:var(--shadow-md);margin-bottom:1.75rem;position:relative;overflow:hidden;">
            <div style="position:relative;z-index:2;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.25rem;">
                <div>
                    <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.2);backdrop-filter:blur(10px);padding:.3rem .85rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:600;margin-bottom:.75rem;color:#dcfce7;">
                        🌱 WasteWise Eco Dashboard
                    </div>
                    <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(1.5rem, 3vw, 1.85rem);font-weight:800;line-height:1.2;margin-bottom:.4rem;">
                        Welcome back, {{ $user->name }}!
                    </h1>
                    <p style="color:#dcfce7;font-size:.925rem;max-width:520px;line-height:1.5;">
                        Logged in as <strong style="color:#fff;">{{ $user->email }}</strong>. Track your waste search activities and discover eco-friendly disposal solutions.
                    </p>
                </div>

                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <a href="{{ route('home') }}" style="background:#fff;color:#065f46;padding:.6rem 1.25rem;border-radius:var(--radius-full);font-weight:700;font-size:.875rem;display:inline-flex;align-items:center;gap:.4rem;box-shadow:var(--shadow-sm);transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                        🔍 Search Waste Items
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Search History Section -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-light);">
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:var(--text-dark);display:flex;align-items:center;gap:.6rem;">
                        📋 Recent Search History
                    </h2>
                    <p style="color:var(--text-muted);font-size:.9rem;margin-top:.2rem;">
                        Items you searched while logged in (newest first)
                    </p>
                </div>
                <span style="background:var(--primary-pale);color:var(--primary-dark);padding:.3rem .8rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:700;">
                    {{ $recentSearches->count() }} {{ Str::plural('record', $recentSearches->count()) }}
                </span>
            </div>

            @if($recentSearches->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:1rem;opacity:.7;">🔍</div>
                    <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">No searches recorded yet</h3>
                    <p style="font-size:.95rem;max-width:400px;margin:0 auto 1.5rem;">
                        Search for waste items like <em>"Old Laptop"</em> or <em>"Plastic Bottle"</em> from the home page to start tracking your disposal history.
                    </p>
                    <a href="{{ route('home') }}" style="background:var(--primary);color:#fff;padding:.7rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;display:inline-block;box-shadow:var(--shadow-sm);">
                        Start Searching Now 🌿
                    </a>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;border-top-left-radius:var(--radius-md);">#</th>
                                <th style="padding:.9rem 1rem;">Waste Item</th>
                                <th style="padding:.9rem 1rem;">Category</th>
                                <th style="padding:.9rem 1rem;">Disposal Method</th>
                                <th style="padding:.9rem 1rem;border-top-right-radius:var(--radius-md);">Searched At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSearches as $index => $history)
                                <tr style="border-bottom:1px solid var(--border-light);transition:background .15s;" onmouseover="this.style.background='var(--bg-card)';" onmouseout="this.style.background='transparent';">
                                    <td style="padding:1rem;color:var(--text-light);font-weight:600;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);">
                                        {{ $history->wasteItem ? $history->wasteItem->name : 'N/A' }}
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($history->wasteItem && $history->wasteItem->category)
                                            <span style="background:var(--primary-pale);color:var(--primary-dark);padding:.25rem .7rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;">
                                                📦 {{ $history->wasteItem->category->name }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted); font-size:.85rem;">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-mid);">
                                        {{ $history->wasteItem && $history->wasteItem->disposalMethod ? $history->wasteItem->disposalMethod->name : 'Standard Recycling' }}
                                    </td>
                                    <td style="padding:1rem;color:var(--text-muted);font-size:.85rem;white-space:nowrap;">
                                        🕒 {{ \Carbon\Carbon::parse($history->searched_at)->format('M d, Y • h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

    </div>
</section>
@endsection
