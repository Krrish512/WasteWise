<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * A category has many WasteItems.
     */
    public function wasteItems()
    {
        return $this->hasMany(WasteItem::class, 'category_id');
    }

    /**
     * A category has many CollectionCentres (FK: category_id) - backward compatible.
     */
    public function collectionCentres()
    {
        return $this->hasMany(CollectionCentre::class, 'category_id');
    }

    /**
     * Many-to-many collection centres relationship via pivot table.
     */
    public function centres()
    {
        return $this->belongsToMany(
            CollectionCentre::class,
            'collection_centre_waste_category',
            'waste_category_id',
            'collection_centre_id'
        )->withTimestamps();
    }

    /**
     * A category has many KnowledgeArticles (FK: waste_category_id).
     */
    public function knowledgeArticles()
    {
        return $this->hasMany(KnowledgeArticle::class, 'waste_category_id');
    }
}
