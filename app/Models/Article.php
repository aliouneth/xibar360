<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title_fr', 'title_en', 'summary_fr', 'summary_en',
        'content_fr', 'content_en', 'category_id', 'source_url',
        'external_id', 'source_name', 'thumbnail', 'publication_date',
        'language', 'source_type', 'is_published', 'is_featured', 'user_id',
    ];

    protected $casts = [
        'publication_date' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    public function scopeByLanguage($query, $lang)
    {
        return $query->where('language', $lang);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title_fr', 'like', "%{$term}%")
              ->orWhere('title_en', 'like', "%{$term}%")
              ->orWhere('content_fr', 'like', "%{$term}%")
              ->orWhere('content_en', 'like', "%{$term}%");
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeImported($query)
    {
        return $query->where('source_type', 'imported');
    }

    public function scopeLocal($query)
    {
        return $query->where('source_type', 'local');
    }

    public function getTitle($lang)
    {
        return $lang === 'fr' ? $this->title_fr : $this->title_en ?? $this->title_fr;
    }

    public function getSummary($lang)
    {
        return $lang === 'fr' ? $this->summary_fr : $this->summary_en ?? $this->summary_fr;
    }

    public function getContent($lang)
    {
        return $lang === 'fr' ? $this->content_fr : $this->content_en ?? $this->content_fr;
    }
}
