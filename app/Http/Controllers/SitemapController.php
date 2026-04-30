<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()->latest('published_at')->get(['slug', 'updated_at']);
        $categories = Category::all(['slug', 'updated_at']);
        $tags = Tag::all(['slug', 'updated_at']);

        return response()->view('sitemap.index', compact('posts', 'categories', 'tags'))
            ->header('Content-Type', 'text/xml');
    }
}
