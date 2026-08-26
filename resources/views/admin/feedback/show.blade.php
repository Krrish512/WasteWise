@extends('layouts.app')

@section('title', 'Feedback Details – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:700px;">
        
        <a href="{{ route('admin.feedback.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;display:inline-block;margin-bottom:1rem;">← Back to Feedback List</a>

        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-md);">
            
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-light);flex-wrap:wrap;gap:1rem;">
                <div>
                    <h1 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);">
                        💬 Feedback Detail #{{ $feedback->id }}
                    </h1>
                    <p style="color:var(--text-muted);font-size:.85rem;margin-top:.2rem;">
                        Submitted on {{ $feedback->created_at ? $feedback->created_at->format('F d, Y • h:i A') : '—' }}
                    </p>
                </div>

                <div style="display:flex;gap:.5rem;align-items:center;">
                    <form action="{{ route('admin.feedback.toggle-reviewed', $feedback) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="background:{{ $feedback->is_reviewed ? '#f1f5f9' : '#dcfce7' }};color:{{ $feedback->is_reviewed ? '#475569' : '#15803d' }};border:1px solid {{ $feedback->is_reviewed ? '#cbd5e1' : '#86efac' }};padding:.5rem 1rem;border-radius:var(--radius-full);font-weight:700;font-size:.85rem;cursor:pointer;">
                            {{ $feedback->is_reviewed ? 'Unmark Reviewed' : '✓ Mark as Reviewed' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.feedback.destroy', $feedback) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this feedback entry?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:.5rem 1rem;border-radius:var(--radius-full);font-weight:700;font-size:.85rem;cursor:pointer;">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- User Info Grid -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;background:var(--bg-base);padding:1.25rem;border-radius:var(--radius-lg);border:1px solid var(--border-light);">
                <div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Contributor Name</div>
                    <div style="font-size:1.05rem;font-weight:700;color:var(--text-dark);margin-top:.2rem;">
                        {{ $feedback->user ? $feedback->user->name : ($feedback->name ?: 'Anonymous Guest') }}
                    </div>
                </div>
                <div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Email Address</div>
                    <div style="font-size:1.05rem;font-weight:700;color:var(--text-dark);margin-top:.2rem;">
                        {{ $feedback->user ? $feedback->user->email : ($feedback->email ?: 'Not provided') }}
                    </div>
                </div>
            </div>

            <!-- Ratings Grid -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;margin-bottom:1.5rem;">
                <div style="background:#fff7ed;border:1px solid #fed7aa;padding:1rem;border-radius:var(--radius-md);">
                    <div style="font-size:.8rem;color:#c2410c;font-weight:600;">Overall Rating</div>
                    <div style="font-size:1.4rem;font-weight:800;color:#9a3412;margin-top:.2rem;">
                        {{ str_repeat('⭐', $feedback->rating) }} ({{ $feedback->rating }}/5)
                    </div>
                </div>
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;padding:1rem;border-radius:var(--radius-md);">
                    <div style="font-size:.8rem;color:#15803d;font-weight:600;">Ease of Use</div>
                    <div style="font-size:1.2rem;font-weight:800;color:#166534;margin-top:.2rem;">
                        {{ $feedback->ease_of_use }}/5
                    </div>
                </div>
                <div style="background:#eff6ff;border:1px solid #bfdbfe;padding:1rem;border-radius:var(--radius-md);">
                    <div style="font-size:.8rem;color:#1d4ed8;font-weight:600;">Helpful &amp; Recommend</div>
                    <div style="font-size:.95rem;font-weight:700;color:#1e40af;margin-top:.3rem;">
                        Helpful: {{ $feedback->helpful ? 'Yes 👍' : 'No 👎' }}<br>
                        Recommend: {{ $feedback->recommend ? 'Yes 🌿' : 'No' }}
                    </div>
                </div>
            </div>

            <!-- Review Text -->
            <div style="margin-bottom:1.5rem;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;">📝 Experience &amp; Review Text</h3>
                <div style="background:#f8fafc;border:1px solid var(--border-light);padding:1.25rem;border-radius:var(--radius-md);font-size:.95rem;color:var(--text-dark);line-height:1.6;">
                    "{{ $feedback->experience }}"
                </div>
            </div>

            <!-- Suggestion Text -->
            @if($feedback->suggestion)
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;">💡 Suggestion for Improvement</h3>
                    <div style="background:#fefce8;border:1px solid #fef08a;padding:1.25rem;border-radius:var(--radius-md);font-size:.95rem;color:#854d0e;line-height:1.6;">
                        "{{ $feedback->suggestion }}"
                    </div>
                </div>
            @endif

        </div>

    </div>
</section>
@endsection
