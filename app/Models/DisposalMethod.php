<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisposalMethod extends Model
{
    protected $fillable = [
        'name',
        'instructions',
        'avoid',
    ];

    /**
     * A disposal method has many WasteItems.
     */
    public function wasteItems()
    {
        return $this->hasMany(WasteItem::class, 'disposal_method_id');
    }
}
