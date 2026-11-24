@extends('layouts.app')

@section('title', 'Edit Collection Centre – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container" style="max-width:800px;">
        
        <a href="{{ route('admin.collection-centres.index') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;display:inline-block;margin-bottom:1rem;text-decoration:none;">← Back to Collection Centres</a>

        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-md);">
            <h1 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);margin-bottom:1.5rem;">
                ✏️ Edit Collection Centre
            </h1>

            <form action="{{ route('admin.collection-centres.update', $centre) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Section: Basic Information -->
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-dark);border-bottom:2px solid var(--border-light);padding-bottom:.5rem;margin-bottom:1.25rem;">
                    🏢 Basic Information
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Centre Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $centre->name) }}" required maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
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
                                <option value="{{ $city->id }}" {{ old('city_id', $centre->city_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }} {{ $city->district ? "({$city->district})" : ($city->state ? "({$city->state})" : '') }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">District</label>
                        <input type="text" name="district" value="{{ old('district', $centre->district) }}" maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;" placeholder="e.g. Surat District">
                        @error('district')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Section: Waste Categories -->
                @php
                    $selectedCategoryIds = old('category_ids', $centre->categories ? $centre->categories->pluck('id')->toArray() : []);
                    if (empty($selectedCategoryIds) && $centre->category_id) {
                        $selectedCategoryIds = [$centre->category_id];
                    }
                @endphp
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.5rem;color:var(--text-dark);">
                        Waste Categories Handled <span style="color:#dc2626;">*</span>
                        <span style="font-weight:normal;color:var(--text-muted);font-size:.85rem;">(Select one or multiple)</span>
                    </label>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:.6rem;padding:1rem;border:1px solid var(--border);border-radius:var(--radius-md);background:#fbfbfb;">
                        @foreach($categories as $category)
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.9rem;color:var(--text-dark);">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                    {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('category_id')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                    @error('category_ids')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Full Address <span style="color:#dc2626;">*</span></label>
                    <textarea name="address" rows="3" required style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">{{ old('address', $centre->address) }}</textarea>
                    @error('address')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Phone / Contact</label>
                    <input type="text" name="phone" value="{{ old('phone', $centre->phone) }}" maxlength="50" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                    @error('phone')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Section: Coordinates & Staging -->
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-dark);border-bottom:2px solid var(--border-light);padding-bottom:.5rem;margin-bottom:1.25rem;">
                    📍 Geographic Coordinates (Optional)
                </div>
                <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:1rem;">
                    Government authorised recyclers or staged facilities without verified GPS coordinates can be left blank safely.
                </p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Latitude</label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude', $centre->latitude) }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                        @error('latitude')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Longitude</label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude', $centre->longitude) }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                        @error('longitude')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Section: Facility Classification & Authorization -->
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-dark);border-bottom:2px solid var(--border-light);padding-bottom:.5rem;margin-bottom:1.25rem;">
                    🛡️ Facility Classification & Authorization
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Facility Type</label>
                        <select name="facility_type" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            @php
                                $facilityTypes = [
                                    'public_dropoff'      => 'Public Drop-off',
                                    'municipal_centre'    => 'Municipal Centre',
                                    'authorized_recycler' => 'Authorized Recycler',
                                    'processing_facility' => 'Processing Facility',
                                    'pickup_provider'     => 'Pickup Provider',
                                    'industrial_facility' => 'Industrial Facility',
                                ];
                            @endphp
                            @foreach($facilityTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('facility_type', $centre->facility_type ?? 'public_dropoff') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('facility_type')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Public Access</label>
                        <select name="public_access" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            @php
                                $accessTypes = [
                                    'public_dropoff'           => 'Public Drop-off',
                                    'business_industrial_only' => 'Business / Industrial Only',
                                    'pickup_only'              => 'Pickup Only',
                                    'unclear'                  => 'Unclear',
                                ];
                            @endphp
                            @foreach($accessTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('public_access', $centre->public_access ?? 'public_dropoff') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('public_access')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Authorization Status</label>
                        <select name="authorization_status" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            @php
                                $authStatuses = [
                                    'verified'           => 'Verified',
                                    'needs_confirmation' => 'Needs Confirmation',
                                    'expired'            => 'Expired',
                                    'unknown'            => 'Unknown',
                                ];
                            @endphp
                            @foreach($authStatuses as $key => $label)
                                <option value="{{ $key }}" {{ old('authorization_status', $centre->authorization_status ?? 'verified') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('authorization_status')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Authorization ID (e.g., GPCB ID)</label>
                        <input type="text" name="authorization_id" value="{{ old('authorization_id', $centre->authorization_id) }}" maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                        @error('authorization_id')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Authorization Valid Until</label>
                        <input type="date" name="authorization_valid_until" value="{{ old('authorization_valid_until', $centre->authorization_valid_until ? $centre->authorization_valid_until->format('Y-m-d') : '') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                        @error('authorization_valid_until')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Verification Date</label>
                        <input type="date" name="verification_date" value="{{ old('verification_date', $centre->verification_date ? $centre->verification_date->format('Y-m-d') : '') }}" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                        @error('verification_date')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Confidence Level</label>
                        <select name="confidence" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            <option value="high" {{ old('confidence', $centre->confidence ?? 'medium') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ old('confidence', $centre->confidence ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ old('confidence', $centre->confidence ?? 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                        @error('confidence')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Lifecycle Status</label>
                        <select name="status" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;background:#fff;">
                            <option value="active" {{ old('status', $centre->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending_verification" {{ old('status', $centre->status ?? 'active') == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="archived" {{ old('status', $centre->status ?? 'active') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Section: Web Presence & Notes -->
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-dark);border-bottom:2px solid var(--border-light);padding-bottom:.5rem;margin-bottom:1.25rem;">
                    🌐 Web Presence & Internal Notes
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Website</label>
                        <input type="text" name="website" value="{{ old('website', $centre->website) }}" maxlength="255" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                        @error('website')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Source URL / Reference</label>
                        <input type="text" name="source_url" value="{{ old('source_url', $centre->source_url) }}" maxlength="500" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">
                        @error('source_url')
                            <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom:1.75rem;">
                    <label style="display:block;font-weight:600;margin-bottom:.4rem;color:var(--text-dark);">Internal Notes / Staging Remarks</label>
                    <textarea name="notes" rows="3" style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.95rem;">{{ old('notes', $centre->notes) }}</textarea>
                    @error('notes')
                        <span style="color:#dc2626;font-size:.85rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:flex;gap:1rem;">
                    <button type="submit" style="background:var(--primary);color:#fff;padding:.75rem 1.6rem;border:none;border-radius:var(--radius-full);font-weight:700;font-size:.95rem;cursor:pointer;">
                        💾 Update Centre
                    </button>
                    <a href="{{ route('admin.collection-centres.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:600;font-size:.95rem;text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
