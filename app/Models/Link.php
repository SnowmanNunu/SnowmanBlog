<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Link extends Model
{
    protected $fillable = ['name', 'url', 'description', 'is_visible', 'sort_order'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::tags(['links'])->flush();
        });

        static::deleted(function () {
            Cache::tags(['links'])->flush();
        });
    }
}
