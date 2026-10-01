<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name_fr', 'name_en', 'slug', 'icon', 'description', 'is_active'];

    public function getRouteKeyName(): string
    {
        return 'name_fr';
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLanguage($query, $lang)
    {
        return $query->where('language', $lang);
    }
}
