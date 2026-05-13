<?php

namespace App\Http\Controllers;

use App\Models\Series;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::withCount('publishedPosts')
            ->orderBy('sort_order')
            ->orderByDesc('published_posts_count')
            ->get();

        return view('blog.series.index', compact('series'));
    }

    public function show(string $slug)
    {
        $series = Series::where('slug', $slug)->firstOrFail();
        $posts = $series->publishedPosts()->paginate(10);

        return view('blog.series.show', compact('series', 'posts'));
    }
}
