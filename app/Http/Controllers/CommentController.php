<?php

namespace App\Http\Controllers;

use App\Mail\CommentReplyMail;
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

        // 防刷：同一 IP 60 秒内不能提交相同内容
        $recentDuplicate = Comment::where('ip', $request->ip())
            ->where('content', $validated['content'])
            ->where('created_at', '>', now()->subSeconds(60))
            ->exists();

        if ($recentDuplicate) {
            return back()->with('error', '评论提交过于频繁，请稍后再试');
        }

        $isAdmin = auth()->check();

        $comment = Comment::create([
            ...$validated,
            'content' => clean($validated['content']),
            'post_id' => $post->id,
            'ip' => $request->ip(),
            'is_approved' => $isAdmin,
        ]);

        // 通知被回复的评论者
        if (! empty($validated['parent_id'])) {
            $parentComment = Comment::find($validated['parent_id']);
            if ($parentComment && $parentComment->email && $parentComment->email !== ($validated['email'] ?? null)) {
                try {
                    Mail::to($parentComment->email)->send(new CommentReplyMail($comment, $parentComment));
                } catch (\Throwable $e) {
                    \Log::error('Comment reply notification email failed: '.$e->getMessage());
                }
            }
        }

        if (! $isAdmin) {
            try {
                $adminEmail = Setting::get('admin_email') ?? User::first()?->email;
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new NewCommentMail($comment));
                }
            } catch (\Throwable $e) {
                \Log::error('Comment notification email failed: '.$e->getMessage());
            }
        }

        $message = $isAdmin ? '回复已发布！' : '评论提交成功，等待审核！';

        return back()->with('success', $message);
    }
}
