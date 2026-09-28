<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'ads';

    protected $fillable = ['title', 'description', 'image', 'html_snippet', 'target_url', 'zone', 'is_active', 'order'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByZone($query, $zone)
    {
        return $query->where('zone', $zone);
    }
}
