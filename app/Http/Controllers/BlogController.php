<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::published()
            ->with(['category', 'user', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('blog.show', compact('post'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts', 'category'));
    }

    public function tag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $tag->posts()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts', 'tag'));
    }
}