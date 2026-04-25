<?php

namespace App\Http\Controllers;

use App\Models\Post;

class RssController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->limit(20)
            ->get();

        return response()
            ->view('rss.index', compact('posts'))
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}