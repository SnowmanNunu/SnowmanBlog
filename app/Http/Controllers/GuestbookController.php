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