<?php

namespace App\Http\Controllers;

use App\Models\Post;
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

    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        if (empty($keyword) || mb_strlen($keyword) < 2) {
            return response()->json([]);
        }

        $posts = Post::published()
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%")
                      ->orWhere('excerpt', 'like', "%{$keyword}%");
            })
            ->with('category')
            ->latest('published_at')
            ->limit(10)
            ->get()
            ->map(function ($post) use ($keyword) {
                return [
                    'slug' => $post->slug,
                    'title' => $this->highlight($post->title, $keyword),
                    'excerpt' => $this->highlight($this->getExcerpt($post, $keyword), $keyword),
                    'cover_image' => $post->cover_image ? asset('storage/' . $post->cover_image) : null,
                    'published_at' => $post->published_at->format('Y-m-d'),
                    'category_name' => $post->category->name,
                    'category_slug' => $post->category->slug,
                ];
            });

        return response()->json($posts);
    }

    private function highlight(string $text, string $keyword): string
    {
        $escaped = preg_quote($keyword, '/');
        return preg_replace("/({$escaped})/iu", '<mark class="bg-yellow-200 text-yellow-900 px-0.5 rounded">$1</mark>", e($text));
    }

    private function getExcerpt(Post $post, string $keyword): string
    {
        $text = $post->excerpt ?: strip_tags($post->content);
        $pos = mb_stripos($text, $keyword);
        if ($pos === false) {
            return mb_substr($text, 0, 120) . (mb_strlen($text) > 120 ? '...' : '');
        }
        $start = max(0, $pos - 50);
        $excerpt = mb_substr($text, $start, 160);
        return ($start > 0 ? '...' : '') . $excerpt . (mb_strlen($text) > $start + 160 ? '...' : '');
    }
}