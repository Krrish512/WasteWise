<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'state',
    ];

    /**
     * A city has many CollectionCentres.
     */
    public function collectionCentres()
    {
        return $this->hasMany(CollectionCentre::class, 'city_id');
    }
}
