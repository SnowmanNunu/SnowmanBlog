<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function show(string $slug)
    {
        $series = Series::where('slug', $slug)->firstOrFail();
        $posts = $series->publishedPosts()->paginate(10);

        return view('blog.series.show', compact('series', 'posts'));
    }
}
