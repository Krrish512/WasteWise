@extends('layouts.app')

@section('title', 'Edit Announcement – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:720px;">
        
        <!-- Header -->
        <div style="margin-bottom:2rem;">
            <a href="{{ route('admin.announcements.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Announcements</a>
            <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                ✏️ Edit Announcement
            </h1>
            <p style="color:var(--text-muted);font-size:.95rem;">Update title, content, or publication status</p>
        </div>

        <!-- Form Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div style="margin-bottom:1.5rem;">
                    <label for="title" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Title <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}" required placeholder="e.g. New E-Waste Drop-off Location" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;">
                    @error('title')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug preview -->
                <div style="margin-bottom:1.5rem;background:#f8fafc;padding:.75rem 1rem;border-radius:var(--radius-md);border:1px solid var(--border-light);font-size:.88rem;color:var(--text-muted);">
                    <strong>Current URL Slug:</strong> <code>{{ $announcement->slug }}</code>
                </div>

                <!-- Content -->
                <div style="margin-bottom:1.5rem;">
                    <label for="content" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Content <span style="color:#dc2626;">*</span>
                    </label>
                    <textarea name="content" id="content" rows="6" required placeholder="Write full announcement content here..." style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;font-family:inherit;line-height:1.5;">{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Current Image & Image Upload -->
                <div style="margin-bottom:1.5rem;">
                    <label for="image" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Featured Image
                    </label>

                    @if($announcement->image)
                        <div style="margin-bottom:1rem;display:flex;align-items:center;gap:1rem;background:#f8fafc;padding:.75rem;border-radius:var(--radius-md);border:1px solid var(--border-light);">
                            <img src="{{ asset($announcement->image) }}" alt="Current Image" style="width:80px;height:60px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border);">
                            <div>
                                <div style="font-size:.85rem;font-weight:600;color:var(--text-dark);">Current image uploaded</div>
                                <div style="font-size:.78rem;color:var(--text-muted);">Upload a new file below to replace it.</div>
                            </div>
                        </div>
                    @endif

                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="width:100%;padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#f8fafc;">
                    @error('image')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Published At Date -->
                <div style="margin-bottom:1.5rem;">
                    <label for="published_at" style="display:block;font-weight:700;color:var(--text-dark);margin-bottom:.4rem;font-size:.95rem;">
                        Publication Date
                    </label>
                    <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:1rem;">
                    @error('published_at')
                        <div style="color:#dc2626;font-size:.85rem;margin-top:.3rem;font-weight:500;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Is Published Toggle -->
                <div style="margin-bottom:2rem;background:var(--bg-base);padding:1rem;border-radius:var(--radius-md);border:1px solid var(--border-light);">
                    <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }} style="width:20px;height:20px;accent-color:var(--primary);">
                        <div>
                            <div style="font-weight:700;color:var(--text-dark);font-size:1rem;">Published</div>
                            <div style="font-size:.85rem;color:var(--text-muted);">Uncheck to set status to draft and hide from public updates page.</div>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <div style="display:flex;gap:1rem;justify-content:flex-end;">
                    <a href="{{ route('admin.announcements.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;">
                        Cancel
                    </a>
                    <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.75rem 1.8rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;cursor:pointer;box-shadow:var(--shadow-sm);">
                        💾 Update Announcement
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
