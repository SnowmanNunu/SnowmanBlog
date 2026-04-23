<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'website' => 'nullable|url|max:200',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            ...$validated,
            'post_id' => $post->id,
            'ip' => $request->ip(),
            'is_approved' => true,
        ]);

        return back()->with('success', '评论提交成功！');
    }
}