<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostLike extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'ip_address', 'user_agent'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
