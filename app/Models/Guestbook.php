<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Guestbook extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nickname', 'email', 'website', 'content',
        'reply', 'replied_at', 'is_approved', 'ip',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "留言已{$eventName}")
            ->useLogName('guestbook');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }

    public function isReplied(): bool
    {
        return ! is_null($this->reply);
    }
}
