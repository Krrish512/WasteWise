@extends('layouts.app')

@section('title', 'Add Category – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:650px;">
        
        <a href="{{ route('admin.categories.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;display:inline-block;margin-bottom:1rem;">← Back to Categories</a>

        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-md);">
            <h1 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);margin-bottom:1.5rem;">
                ✨ Add New Waste Category
            </h1>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Category Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. Electronic Waste">
                    @error('name')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1.75rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Description</label>
                    <textarea name="description" rows="4" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="Brief overview of items belonging to this category...">{{ old('description') }}</textarea>
                    @error('description')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:flex;gap:1rem;">
                    <button type="submit" style="background:var(--primary);color:#fff;padding:.75rem 1.6rem;border:none;border-radius:var(--radius-full);font-weight:700;font-size:.95rem;cursor:pointer;">
                        💾 Save Category
                    </button>
                    <a href="{{ route('admin.categories.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:600;font-size:.95rem;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
