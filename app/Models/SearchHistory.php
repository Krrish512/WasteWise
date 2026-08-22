<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'waste_item_id',
        'searched_at',
    ];

    /**
     * A search history entry belongs to a User (FK: user_id).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A search history entry belongs to a WasteItem (FK: waste_item_id).
     */
    public function wasteItem()
    {
        return $this->belongsTo(WasteItem::class, 'waste_item_id');
    }
}
