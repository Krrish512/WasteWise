<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'keywords',
        'category_id',
        'disposal_method_id',
    ];

    /**
     * A waste item belongs to a WasteCategory (FK: category_id).
     */
    public function category()
    {
        return $this->belongsTo(WasteCategory::class, 'category_id');
    }

    /**
     * A waste item belongs to a DisposalMethod (FK: disposal_method_id).
     */
    public function disposalMethod()
    {
        return $this->belongsTo(DisposalMethod::class, 'disposal_method_id');
    }

    /**
     * A waste item has many SearchHistories.
     */
    public function searchHistories()
    {
        return $this->hasMany(SearchHistory::class, 'waste_item_id');
    }
}
