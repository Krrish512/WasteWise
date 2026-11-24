<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionCentre extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city_id',
        'category_id',
        'phone',
        'latitude',
        'longitude',
        'district',
        'facility_type',
        'public_access',
        'authorization_status',
        'authorization_id',
        'authorization_valid_until',
        'website',
        'source_url',
        'verification_date',
        'confidence',
        'status',
        'notes',
    ];

    protected $casts = [
        'latitude'                  => 'decimal:7',
        'longitude'                 => 'decimal:7',
        'authorization_valid_until' => 'date',
        'verification_date'         => 'date',
    ];

    /**
     * A collection centre belongs to a City (FK: city_id).
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Backward-compatible single category relationship (FK: category_id).
     */
    public function category()
    {
        return $this->belongsTo(WasteCategory::class, 'category_id');
    }

    /**
     * Many-to-many relationship with waste categories via pivot table.
     */
    public function categories()
    {
        return $this->belongsToMany(
            WasteCategory::class,
            'collection_centre_waste_category',
            'collection_centre_id',
            'waste_category_id'
        )->withTimestamps();
    }
}
