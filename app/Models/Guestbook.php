<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guestbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'nickname', 'email', 'website', 'content',
        'reply', 'replied_at', 'is_approved', 'ip'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'replied_at' => 'datetime',
    ];

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
        return !is_null($this->reply);
    }
}