@extends('layouts.app')

@section('title', 'User Feedback & Suggestions – WasteWise')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:720px;">
        
        <!-- Hero Header -->
        <div style="text-align:center;margin-bottom:2.5rem;">
            <div style="display:inline-flex;align-items:center;gap:.5rem;background:var(--primary-pale);color:var(--primary-dark);padding:.35rem 1rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:700;margin-bottom:1rem;">
                💬 Community Feedback
            </div>
            <h1 style="font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;color:var(--text-dark);line-height:1.2;margin-bottom:.6rem;">
                User Feedback &amp; Suggestions
            </h1>
            <p style="color:var(--text-muted);font-size:1.05rem;max-width:550px;margin:0 auto;">
                Share your experience using WasteWise. Your feedback helps us build a cleaner, greener waste management ecosystem.
            </p>
        </div>

        <!-- Form Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-md);">
            
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf

                <!-- Contact Info (Prefilled if logged in, optional for guests) -->
                @guest
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                        <div>
                            <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Your Name (Optional)</label>
                            <input type="text" name="name" value="{{ old('name') }}" maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. Soumya Sharma">
                            @error('name')
                                <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Email Address (Optional)</label>
                            <input type="email" name="email" value="{{ old('email') }}" maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. soumya@example.com">
                            @error('email')
                                <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @else
                    <div style="background:var(--bg-base);border:1px solid var(--border-light);padding:1rem 1.25rem;border-radius:var(--radius-md);margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.3rem;">👤</span>
                        <div>
                            <div style="font-weight:700;color:var(--text-dark);font-size:.95rem;">Submitting as {{ auth()->user()->name }}</div>
                            <div style="font-size:.85rem;color:var(--text-muted);">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                @endguest

                <!-- Overall Experience Rating -->
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.5rem;color:var(--text-dark);">1. Overall Experience Rating <span style="color:#dc2626;">*</span></label>
                    <div style="display:flex;gap:1.5rem;flex-wrap:wrap;background:var(--bg-base);padding:1rem;border-radius:var(--radius-md);border:1px solid var(--border-light);">
                        @for($i = 5; $i >= 1; $i--)
                            <label style="display:flex;align-items:center;gap:.4rem;font-weight:600;cursor:pointer;color:var(--text-dark);">
                                <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', '5') == $i ? 'checked' : '' }} style="accent-color:var(--primary);">
                                <span>{{ $i }} ⭐</span>
                            </label>
                        @endfor
                    </div>
                    @error('rating')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Ease of Use Rating -->
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.5rem;color:var(--text-dark);">2. Ease of Use Rating <span style="color:#dc2626;">*</span></label>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;background:var(--bg-base);padding:1rem;border-radius:var(--radius-md);border:1px solid var(--border-light);">
                        @foreach([1 => '1 - Very Difficult', 2 => '2 - Difficult', 3 => '3 - Moderate', 4 => '4 - Easy', 5 => '5 - Very Easy'] as $val => $label)
                            <label style="display:flex;align-items:center;gap:.4rem;font-weight:600;cursor:pointer;font-size:.9rem;color:var(--text-dark);">
                                <input type="radio" name="ease_of_use" value="{{ $val }}" {{ old('ease_of_use', '5') == $val ? 'checked' : '' }} style="accent-color:var(--primary);">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('ease_of_use')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Was disposal information helpful? -->
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.5rem;color:var(--text-dark);">3. Was the disposal information helpful? <span style="color:#dc2626;">*</span></label>
                    <div style="display:flex;gap:2rem;">
                        <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;cursor:pointer;">
                            <input type="radio" name="helpful" value="1" {{ old('helpful', '1') == '1' ? 'checked' : '' }} style="accent-color:var(--primary);">
                            <span>Yes 👍</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;cursor:pointer;">
                            <input type="radio" name="helpful" value="0" {{ old('helpful') === '0' ? 'checked' : '' }} style="accent-color:var(--primary);">
                            <span>No 👎</span>
                        </label>
                    </div>
                    @error('helpful')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Experience / Review Textarea -->
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">4. Experience / Review <span style="color:#dc2626;">*</span></label>
                    <textarea name="experience" rows="4" required maxlength="2000" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="Tell us about your experience searching or finding disposal guidance on WasteWise...">{{ old('experience') }}</textarea>
                    @error('experience')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Suggestion for Improvement -->
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">5. Suggestion for Improvement (Optional)</label>
                    <textarea name="suggestion" rows="3" maxlength="2000" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="Any features, new waste categories, or collection centres you would like to see?">{{ old('suggestion') }}</textarea>
                    @error('suggestion')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Would you recommend WasteWise? -->
                <div style="margin-bottom:2rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.5rem;color:var(--text-dark);">6. Would you recommend WasteWise to others? <span style="color:#dc2626;">*</span></label>
                    <div style="display:flex;gap:2rem;">
                        <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;cursor:pointer;">
                            <input type="radio" name="recommend" value="1" {{ old('recommend', '1') == '1' ? 'checked' : '' }} style="accent-color:var(--primary);">
                            <span>Yes, definitely! 🌿</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;cursor:pointer;">
                            <input type="radio" name="recommend" value="0" {{ old('recommend') === '0' ? 'checked' : '' }} style="accent-color:var(--primary);">
                            <span>Not right now</span>
                        </label>
                    </div>
                    @error('recommend')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" style="width:100%;background:linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);color:#fff;padding:.9rem 1.5rem;border:none;border-radius:var(--radius-full);font-weight:800;font-size:1.05rem;cursor:pointer;box-shadow:var(--shadow-md);transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                    🚀 Submit Feedback
                </button>
            </form>

        </div>

    </div>
</section>
@endsection
