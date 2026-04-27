<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $posts = Cache::tags(['posts'])->remember("posts:index:page:{$page}", 300, function () {
            return Post::published()
                ->with(['category', 'user'])
                ->orderByDesc('is_pinned')
                ->latest('published_at')
                ->paginate(10);
        });

        return view('blog.index', compact('posts'));
    }

    public function show(Request $request, $slug)
    {
        $post = Post::published()
            ->with(['category', 'user', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $sessionKey = 'viewed_post_' . $post->id;
        if (!session()->has($sessionKey)) {
            $ip = $request->ip();
            $today = now()->toDateString();

            $alreadyViewed = \DB::table('post_views')
                ->where('post_id', $post->id)
                ->where('ip_address', $ip)
                ->whereDate('viewed_at', $today)
                ->exists();

            if (!$alreadyViewed) {
                $post->increment('views');
                \DB::table('post_views')->insert([
                    'post_id' => $post->id,
                    'ip_address' => $ip,
                    'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                    'viewed_at' => now(),
                ]);
            }

            session()->put($sessionKey, true);
        }

        $cacheKey = "post:{$post->id}";

        $prevPost = Cache::tags(['posts'])->remember("{$cacheKey}:prev", 600, function () use ($post) {
            return Post::published()
                ->where('published_at', '<', $post->published_at)
                ->orderBy('published_at', 'desc')
                ->select('id', 'title', 'slug')
                ->first();
        });

        $nextPost = Cache::tags(['posts'])->remember("{$cacheKey}:next", 600, function () use ($post) {
            return Post::published()
                ->where('published_at', '>', $post->published_at)
                ->orderBy('published_at', 'asc')
                ->select('id', 'title', 'slug')
                ->first();
        });

        $relatedPosts = Cache::tags(['posts'])->remember("{$cacheKey}:related", 600, function () use ($post) {
            return Post::published()
                ->where('id', '!=', $post->id)
                ->where(function ($query) use ($post) {
                    $query->where('category_id', $post->category_id)
                          ->orWhereHas('tags', function ($q) use ($post) {
                              $q->whereIn('tags.id', $post->tags->pluck('id'));
                          });
                })
                ->with('category')
                ->select('id', 'title', 'slug', 'published_at', 'cover_image', 'category_id')
                ->latest('published_at')
                ->limit(5)
                ->get();
        });

        return view('blog.show', compact('post', 'prevPost', 'nextPost', 'relatedPosts'));
    }

    public function category($slug)
    {
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();
        $page = request('page', 1);
        $posts = Cache::tags(['posts'])->remember("posts:category:{$slug}:page:{$page}", 300, function () use ($category) {
            return Post::published()
                ->where('category_id', $category->id)
                ->with(['category', 'user'])
                ->orderByDesc('is_pinned')
                ->latest('published_at')
                ->paginate(10);
        });

        return view('blog.index', compact('posts', 'category'));
    }

    public function tag($slug)
    {
        $tag = \App\Models\Tag::where('slug', $slug)->firstOrFail();
        $page = request('page', 1);
        $posts = Cache::tags(['posts'])->remember("posts:tag:{$slug}:page:{$page}", 300, function () use ($tag) {
            return $tag->posts()
                ->published()
                ->with(['category', 'user'])
                ->orderByDesc('is_pinned')
                ->latest('published_at')
                ->paginate(10);
        });

        return view('blog.index', compact('posts', 'tag'));
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        if (empty($keyword) || mb_strlen($keyword) < 2) {
            return response()->json([]);
        }

        $query = Post::published()
            ->with('category')
            ->orderByDesc('is_pinned')
            ->latest('published_at');

        // Try FULLTEXT search first
        $posts = (clone $query)
            ->whereRaw('MATCH(title, content, excerpt) AGAINST(? IN NATURAL LANGUAGE MODE)', [$keyword])
            ->limit(10)
            ->get();

        // Fallback to LIKE if no FULLTEXT results
        if ($posts->isEmpty()) {
            $posts = $query
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                          ->orWhere('content', 'like', "%{$keyword}%")
                          ->orWhere('excerpt', 'like', "%{$keyword}%")
                          ->orWhere('slug', 'like', "%{$keyword}%");
                })
                ->limit(10)
                ->get();
        }

        $posts = $posts->map(function ($post) use ($keyword) {
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
        return preg_replace("/({$escaped})/iu", '<mark class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-semibold px-1 rounded">$1</mark>', e($text));
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
