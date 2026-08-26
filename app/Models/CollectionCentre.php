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
    ];

    /**
     * A collection centre belongs to a City (FK: city_id).
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * A collection centre belongs to a WasteCategory (FK: category_id).
     */
    public function category()
    {
        return $this->belongsTo(WasteCategory::class, 'category_id');
    }
}
