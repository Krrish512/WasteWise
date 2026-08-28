@extends('layouts.app')

@section('title', 'Add Sustainability Tip – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:720px;">
        
        <!-- Header -->
        <div style="margin-bottom:2rem;">
            <a href="{{ route('admin.sustainability-tips.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Sustainability Tips</a>
            <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                🌱 Add Sustainability Tip
            </h1>
            <p style="color:var(--text-muted);font-size:.95rem;">Create a new eco-friendly tip for community guidance</p>
        </div>

        <!-- Form Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.sustainability-tips.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div style="margin-bottom:1.5rem;">
                    <label for="title" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Tip Title <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. 5 Easy Steps to Start Home Composting Today" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;">
                    @error('title')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category -->
                <div style="margin-bottom:1.5rem;">
                    <label for="category" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Category <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="category" id="category" value="{{ old('category', 'Composting') }}" required list="categories-list" placeholder="e.g. Composting, Zero Waste, E-Waste, Energy, Plastics" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;">
                    <datalist id="categories-list">
                        <option value="Composting">
                        <option value="Zero Waste">
                        <option value="E-Waste Reduction">
                        <option value="Plastics & Packaging">
                        <option value="Energy Saving">
                        <option value="Water Conservation">
                        <option value="Food Waste">
                        @foreach($existingCategories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    @error('category')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Summary / Short Excerpt -->
                <div style="margin-bottom:1.5rem;">
                    <label for="summary" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Short Summary / Excerpt <span style="color:var(--text-muted);font-weight:400;">(Optional - Displayed on tip cards)</span>
                    </label>
                    <textarea name="summary" id="summary" rows="2" placeholder="Brief 1-2 sentence preview of the sustainability tip..." style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;font-family:inherit;line-height:1.5;">{{ old('summary') }}</textarea>
                    @error('summary')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content -->
                <div style="margin-bottom:1.5rem;">
                    <label for="content" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Full Tip Detailed Guidance <span style="color:#dc2626;">*</span>
                    </label>
                    <textarea name="content" id="content" rows="8" required placeholder="Write the full detailed eco tip, step-by-step instructions, and benefits..." style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;font-family:inherit;line-height:1.5;">{{ old('content') }}</textarea>
                    @error('content')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div style="margin-bottom:1.5rem;">
                    <label for="image" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Tip Cover Image <span style="color:var(--text-muted);font-weight:400;">(Optional - JPG, PNG, WEBP max 2MB)</span>
                    </label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="width:100%;padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#f8fafc;">
                    @error('image')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Published At Date -->
                <div style="margin-bottom:1.5rem;">
                    <label for="published_at" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Publication Date <span style="color:var(--text-muted);font-weight:400;">(Optional - Defaults to current timestamp if published)</span>
                    </label>
                    <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;">
                    @error('published_at')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Is Published Toggle -->
                <div style="margin-bottom:2rem;background:var(--bg-base);padding:1rem;border-radius:var(--radius-md);border:1px solid var(--border-light);">
                    <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} style="width:20px;height:20px;accent-color:var(--primary);">
                        <div>
                            <div style="font-weight:700;color:var(--text-dark);font-size:1rem;">Publish Immediately</div>
                            <div style="font-size:.85rem;color:var(--text-muted);">When checked, this sustainability tip will be immediately visible on the public website.</div>
                        </div>
                    </label>
                </div>

                <!-- Submit Buttons -->
                <div style="display:flex;gap:1rem;justify-content:flex-end;">
                    <a href="{{ route('admin.sustainability-tips.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;">
                        Cancel
                    </a>
                    <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.75rem 1.8rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;cursor:pointer;box-shadow:var(--shadow-sm);">
                        💾 Save Sustainability Tip
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
