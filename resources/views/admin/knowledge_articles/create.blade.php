@extends('layouts.app')

@section('title', 'Add Knowledge Article – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:800px;">
        
        <!-- Header -->
        <div style="margin-bottom:2rem;">
            <a href="{{ route('admin.knowledge-articles.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Articles List</a>
            <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                ➕ Create Knowledge Article
            </h1>
            <p style="color:var(--text-muted);font-size:.95rem;">Add a new educational article to the Waste Knowledge Hub</p>
        </div>

        <!-- Form Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.knowledge-articles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div style="margin-bottom:1.5rem;">
                    <label for="title" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                        Article Title <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. How to Dispose of Old Electronics Safely" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                    @error('title')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:600;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Waste Category -->
                <div style="margin-bottom:1.5rem;">
                    <label for="category" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                        Waste Category <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="category" id="category" required style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                        <option value="">Select Waste Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:600;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Summary -->
                <div style="margin-bottom:1.5rem;">
                    <label for="summary" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                        Summary <span style="color:var(--text-muted);font-weight:400;">(Short preview statement)</span>
                    </label>
                    <textarea name="summary" id="summary" rows="3" placeholder="Brief explanation of what users will learn from this article..." style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;font-family:inherit;">{{ old('summary') }}</textarea>
                    @error('summary')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:600;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Detailed Content -->
                <div style="margin-bottom:1.5rem;">
                    <label for="content" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                        Detailed Content <span style="color:#dc2626;">*</span>
                    </label>
                    <textarea name="content" id="content" rows="10" required placeholder="Provide comprehensive educational content covering what the waste type is, common examples, safe handling, disposal/recycling steps, and what to avoid..." style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;font-family:inherit;">{{ old('content') }}</textarea>
                    @error('content')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:600;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cover Image -->
                <div style="margin-bottom:1.5rem;">
                    <label for="image" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                        Cover Image <span style="color:var(--text-muted);font-weight:400;">(Optional: JPEG, PNG, JPG, GIF, WEBP up to 2MB)</span>
                    </label>
                    <input type="file" name="image" id="image" accept="image/*" style="width:100%;padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;">
                    @error('image')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:600;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Publication Date & Publish Immediately -->
                <div style="grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;display:grid;">
                    <div>
                        <label for="published_at" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.5rem;">
                            Publication Date
                        </label>
                        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                        @error('published_at')
                            <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:600;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display:flex;align-items:center;padding-top:1.5rem;">
                        <label style="display:inline-flex;align-items:center;gap:.75rem;cursor:pointer;font-weight:700;color:var(--text-dark);">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} style="width:20px;height:20px;accent-color:var(--primary);">
                            <span>Publish Immediately</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display:flex;gap:1rem;justify-content:flex-end;">
                    <a href="{{ route('admin.knowledge-articles.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.5rem;border-radius:var(--radius-full);font-weight:600;font-size:.95rem;text-decoration:none;">
                        Cancel
                    </a>
                    <button type="submit" style="background:var(--primary);color:#fff;padding:.75rem 1.8rem;border-radius:var(--radius-full);font-weight:700;font-size:.95rem;border:none;cursor:pointer;box-shadow:var(--shadow-sm);">
                        💾 Save Article
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>
@endsection
