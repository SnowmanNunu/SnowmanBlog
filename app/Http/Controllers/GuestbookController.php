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
            'email' => 'required|email|max:100',
            'website' => 'nullable|url|max:200',
            'content' => 'required|string|max:2000',
        ]);

        Guestbook::create([
            ...$validated,
            'ip' => $request->ip(),
            'is_approved' => true,
        ]);

        return back()->with('success', '留言提交成功！');
    }
}