<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    /**
     * Explicit table name.
     */
    protected $table = 'feedback';

    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'rating',
        'ease_of_use',
        'helpful',
        'experience',
        'suggestion',
        'recommend',
        'is_reviewed',
    ];

    /**
     * Attributes to cast.
     */
    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'ease_of_use'  => 'integer',
            'helpful'     => 'boolean',
            'recommend'   => 'boolean',
            'is_reviewed' => 'boolean',
        ];
    }

    /**
     * Feedback belongs to a User (nullable for guests).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
