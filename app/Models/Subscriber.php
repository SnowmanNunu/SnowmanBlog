<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'verified_at', 'unsubscribe_token', 'ip'];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }
}
