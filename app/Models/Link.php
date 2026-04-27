<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['name', 'url', 'description', 'is_visible', 'sort_order'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];
}