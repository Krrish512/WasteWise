<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'waste_category_id',
        'title',
        'slug',
        'summary',
        'content',
        'image',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Scope a query to only include published knowledge articles.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * An article belongs to a WasteCategory.
     */
    public function category()
    {
        return $this->belongsTo(WasteCategory::class, 'waste_category_id');
    }

    /**
     * Alias relationship for category.
     */
    public function wasteCategory()
    {
        return $this->belongsTo(WasteCategory::class, 'waste_category_id');
    }
}
