<?php

namespace App\Http\Controllers;

use App\Mail\NewCommentMail;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:200',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $isAdmin = auth()->check();

        $comment = Comment::create([
            ...$validated,
            'post_id' => $post->id,
            'ip' => $request->ip(),
            'is_approved' => $isAdmin,
        ]);

        if (!$isAdmin) {
            try {
                $adminEmail = Setting::get('admin_email') ?? User::first()?->email;
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new NewCommentMail($comment));
                }
            } catch (\Throwable $e) {
                \Log::error('Comment notification email failed: ' . $e->getMessage());
            }
        }

        $message = $isAdmin ? '回复已发布！' : '评论提交成功，等待审核！';
        return back()->with('success', $message);
    }
}