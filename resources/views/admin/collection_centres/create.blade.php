@extends('layouts.app')

@section('title', 'Add Collection Centre – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:700px;">
        
        <a href="{{ route('admin.collection-centres.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;display:inline-block;margin-bottom:1rem;">← Back to Collection Centres</a>

        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-md);">
            <h1 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);margin-bottom:1.5rem;">
                ✨ Add Collection Centre
            </h1>

            <form action="{{ route('admin.collection-centres.store') }}" method="POST">
                @csrf

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Centre Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. GreenEarth Recycling Hub">
                    @error('name')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">City <span style="color:#dc2626;">*</span></label>
                        <select name="city_id" required style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            <option value="">Select City...</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }} {{ $city->state ? "({$city->state})" : '' }}</option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Waste Category <span style="color:#dc2626;">*</span></label>
                        <select name="category_id" required style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            <option value="">Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Full Address <span style="color:#dc2626;">*</span></label>
                    <textarea name="address" rows="3" required style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="Street address, area, landmark...">{{ old('address') }}</textarea>
                    @error('address')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Phone / Contact</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" maxlength="50" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. +91 22 2835 4567">
                    @error('phone')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.75rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Latitude</label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. 19.1136">
                        @error('latitude')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Longitude</label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. 72.8697">
                        @error('longitude')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display:flex;gap:1rem;">
                    <button type="submit" style="background:var(--primary);color:#fff;padding:.75rem 1.6rem;border:none;border-radius:var(--radius-full);font-weight:700;font-size:.95rem;cursor:pointer;">
                        💾 Save Centre
                    </button>
                    <a href="{{ route('admin.collection-centres.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:600;font-size:.95rem;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
