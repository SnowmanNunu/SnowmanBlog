<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Series extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'slug', 'description', 'cover_image', 'sort_order',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "专栏已{$eventName}")
            ->useLogName('series');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy('published_at');
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(Post::class)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at');
    }
}
