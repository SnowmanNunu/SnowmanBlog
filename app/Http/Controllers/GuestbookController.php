<?php

namespace App\Http\Controllers;

use App\Models\Guestbook;
use Illuminate\Http\Request;

class GuestbookController extends Controller
{
    public function index()
    {
        $messages = Guestbook::approved()->recent()->paginate(10);
        return view('guestbook.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:200',
            'content' => 'required|string|max:2000',
        ]);

        // 防刷：同一 IP 60 秒内不能提交相同内容
        $recentDuplicate = Guestbook::where('ip', $request->ip())
            ->where('content', $validated['content'])
            ->where('created_at', '>', now()->subSeconds(60))
            ->exists();

        if ($recentDuplicate) {
            return back()->with('error', '留言提交过于频繁，请稍后再试');
        }

        $isAdmin = auth()->check();

        Guestbook::create([
            ...$validated,
            'ip' => $request->ip(),
            'is_approved' => $isAdmin,
        ]);

        $message = $isAdmin ? '留言已发布！' : '留言提交成功，等待审核！';
        return back()->with('success', $message);
    }
}
