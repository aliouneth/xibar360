<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'name',
        'url',
        'language',
        'enabled',
        'user_agent',
        'last_checked_at',
        'last_status',
        'last_error',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('enabled', false);
    }

    /**
     * Shape expected by WebScannerService.
     *
     * @return array<string, mixed>
     */
    public function toScannerSource(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'language' => $this->language ?: 'fr',
            'enabled' => (bool) $this->enabled,
            'user_agent' => $this->user_agent,
        ];
    }
}
