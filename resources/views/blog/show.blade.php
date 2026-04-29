@extends('layouts.blog')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: ($post->excerpt ? strip_tags($post->excerpt) : ''))
@section('meta_keywords', $post->meta_keywords ?: $post->tags->pluck('name')->implode(','))
@section('canonical', route('blog.show', $post->slug))
@section('og_title', ($post->meta_title ?: $post->title) . ' - ' . $siteTitle)
@section('og_description', $post->meta_description ?: ($post->excerpt ? strip_tags($post->excerpt) : ''))
@section('og_type', 'article')
@if($post->cover_image)
@section('og_image', asset('storage/' . $post->cover_image))
@endif

@php
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->meta_title ?: $post->title,
    'description' => $post->meta_description ?: ($post->excerpt ? strip_tags($post->excerpt) : ''),
    'url' => route('blog.show', $post->slug),
    'datePublished' => $post->published_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $post->user->name,
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $siteTitle,
    ],
];
if ($post->cover_image) {
    $jsonLd['image'] = asset('storage/' . $post->cover_image);
}
@endphp
@php
$breadcrumbLd = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Home'),
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $post->category->name,
            'item' => route('blog.category', $post->category->slug),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ],
    ],
];
@endphp
@section('jsonld', json_encode([$jsonLd, $breadcrumbLd], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))

@section('content')
<style>
.article-content h2 { font-size: 1.5rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #111827; }
.article-content h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #1f2937; }
.article-content p { margin-bottom: 1rem; line-height: 1.75; }
.article-content ul, .article-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
.article-content ul { list-style-type: disc; }
.article-content ol { list-style-type: decimal; }
.article-content blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; color: #6b7280; font-style: italic; margin-bottom: 1rem; }
.article-content pre { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 1rem; position: relative; }
.article-content pre code { background: transparent; padding: 0; }
.article-content code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.875rem; background: #f1f5f9; padding: 0.125rem 0.375rem; border-radius: 0.25rem; color: #ef4444; }
.article-content hr { border: 0; border-top: 1px solid #e5e7eb; margin: 1.5rem 0; }
.article-content a { color: #2563eb; text-decoration: underline; }
.article-content img { border-radius: 0.5rem; margin: 1rem 0; max-width: 100%; cursor: zoom-in; }
.article-content table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
.article-content th, .article-content td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; }
.article-content th { background: #f9fafb; font-weight: 600; }
.copy-code-btn { position: absolute; top: 0.5rem; right: 0.5rem; padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #e2e8f0; background: rgba(255,255,255,0.1); border-radius: 0.375rem; cursor: pointer; transition: all 0.2s; border: none; }
.copy-code-btn:hover { background: rgba(255,255,255,0.2); }

.dark .article-content h2 { color: #f3f4f6; }
.dark .article-content h3 { color: #e5e7eb; }
.dark .article-content blockquote { border-left-color: #374151; color: #9ca3af; }
.dark .article-content code { background: #1f2937; color: #f87171; }
.dark .article-content hr { border-top-color: #374151; }
.dark .article-content th, .dark .article-content td { border-color: #374151; }
.dark .article-content th { background: #1f2937; }
.dark .article-content a { color: #3b82f6; }
</style>

<!-- 阅读进度条 -->
<div id="reading-progress" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 z-50 transition-all duration-100" style="width: 0%"></div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- 文章主体 -->
    <div class="lg:col-span-3">
        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            @if($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg mb-6" loading="lazy">
            @endif

            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $post->title }}</h1>

            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-8 space-x-4">
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $post->user->name }}</span>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span>{{ $post->published_at->format('Y-m-d H:i') }}</span>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">{{ $post->category->name }}</a>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span class="flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ $post->views ?? 0 }}</span>
                </span>
            </div>

            <div class="article-content max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                {!! Str::markdown($post->content) !!}
            </div>

            <div class="mt-10 flex flex-wrap gap-2">
                @foreach($post->tags as $t)
                    <a href="{{ route('blog.tag', $t->slug) }}" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-sm rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">{{ $t->name }}</a>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between flex-wrap gap-4">
                <form action="{{ route('blog.like', $post->slug) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 rounded-full hover:bg-pink-100 dark:hover:bg-pink-900/30 transition-colors border border-pink-100 dark:border-pink-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ $post->likes_count ?? 0 }}</span>
                    </button>
                </form>

                <div class="flex items-center gap-2 flex-wrap">
                    <button onclick="shareWechat()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors text-sm font-medium border border-green-100 dark:border-green-900/30">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.067 5.591-.13.487-.478 1.335-.82 1.89-.073.124-.09.24-.045.347.045.107.15.18.285.195 1.18.135 2.864-.06 3.84-.56a11.87 11.87 0 0 0 2.364.236c.08 0 .16 0 .24-.005.045.45.195.9.435 1.305.51.855 1.35 1.5 2.385 1.83.945.3 1.995.3 2.955.045.81.435 1.89.6 2.82.42.135-.03.24-.105.285-.21.045-.105.03-.225-.045-.345-.33-.54-.66-1.35-.78-1.815 1.665-1.245 2.73-3.09 2.73-5.145 0-4.155-4.005-7.53-8.955-7.53-.225 0-.45.015-.675.03.045-.45.18-.87.435-1.245.51-.765 1.335-1.32 2.34-1.605.99-.285 2.1-.27 3.09.045.135.045.285.03.39-.045a.42.42 0 0 0 .165-.33c-.015-.12-.075-.225-.165-.3-1.08-.915-2.49-1.44-3.99-1.44-.195 0-.39.015-.585.03z"/></svg>
                        微信
                    </button>
                    <a href="https://service.weibo.com/share/share.php?url={{ urlencode(route('blog.show', $post->slug)) }}&title={{ urlencode($post->title) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-sm font-medium border border-red-100 dark:border-red-900/30">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.739 5.443zM9.05 17.219c-.384.616-1.208.884-1.829.602-.612-.279-.793-.991-.406-1.593.379-.595 1.176-.861 1.793-.601.622.263.82.972.442 1.592zm1.27-1.627c-.141.237-.449.353-.689.253-.236-.09-.313-.361-.177-.586.138-.227.436-.346.672-.24.239.09.315.36.194.573zm.176-2.719c-1.893-.493-4.033.45-4.857 2.118-.836 1.704-.026 3.591 1.886 4.21 1.983.64 4.318-.341 5.132-2.179.8-1.793-.201-3.642-2.161-4.149zm7.563-1.224c-.346-.105-.579-.18-.401-.649.386-1.02.426-1.899.003-2.525-.793-1.17-2.966-1.109-5.419-.031 0 0-.776.34-.578-.277.381-1.215.324-2.234-.27-2.822-1.349-1.33-4.937.047-8.014 3.079C1.134 10.611 0 12.775 0 14.665c0 3.608 4.644 5.808 9.183 5.808 5.951 0 9.906-3.454 9.906-6.197 0-1.654-1.396-2.594-2.12-2.827zm.696-6.349c-.666-.747-1.654-1.123-2.774-1.056l-.045.002a.42.42 0 0 0-.405.437c.007.135.06.261.15.357a.69.69 0 0 0 .48.195c.69-.042 1.323.195 1.755.669.429.471.607 1.113.501 1.808a.419.419 0 0 0 .33.49.42.42 0 0 0 .495-.327c.15-.985-.09-1.917-.687-2.575h.2zm1.537 1.318c-.966-1.083-2.403-1.629-4.03-1.53a.42.42 0 1 0 .052.838c1.314-.08 2.445.361 3.24 1.254.793.891 1.14 2.101.978 3.409a.42.42 0 0 0 .487.358.421.421 0 0 0 .358-.488c.198-1.583-.23-3.04-1.085-3.841z"/></svg>
                        微博
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 rounded-full hover:bg-sky-100 dark:hover:bg-sky-900/30 transition-colors text-sm font-medium border border-sky-100 dark:border-sky-900/30">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 0 1-2.825.775 4.958 4.958 0 0 0 2.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 0 0-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 0 0-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 0 1-2.228-.616v.06a4.923 4.923 0 0 0 3.946 4.827 4.996 4.996 0 0 1-2.212.085 4.936 4.936 0 0 0 4.604 3.417 9.867 9.867 0 0 1-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0 0 7.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0 0 24 4.59z"/></svg>
                        Twitter
                    </a>
                    <button onclick="copyLink()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-sm font-medium border border-gray-100 dark:border-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copy-text">复制链接</span>
                    </button>
                </div>
            </div>

            <!-- 微信分享弹窗 -->
            <div id="wechat-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" onclick="if(event.target===this){this.classList.add('hidden');this.classList.remove('flex');}">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg max-w-sm mx-4 text-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">微信扫一扫分享</h3>
                    <img id="wechat-qrcode" src="" alt="微信二维码" class="mx-auto w-48 h-48 rounded-lg border border-gray-200 dark:border-gray-600">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">打开微信扫一扫，分享给好友</p>
                    <button onclick="document.getElementById('wechat-modal').classList.add('hidden');document.getElementById('wechat-modal').classList.remove('flex');" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">关闭</button>
                </div>
            </div>
        </article>

        <!-- {{ __('Previous') }} / {{ __('Next') }} -->
        <div class="mt-6 grid grid-cols-2 gap-3 md:gap-4">
            @if($prevPost)
                <a href="{{ route('blog.show', $prevPost->slug) }}" class="group block p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-200 hover:shadow-md transition-all">
                    <span class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 uppercase tracking-wide flex items-center gap-1">
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        上一篇
                    </span>
                    <p class="mt-1 text-xs md:text-sm font-medium text-gray-900 dark:text-gray-100 truncate md:line-clamp-2 group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ $prevPost->title }}</p>
                </a>
            @else
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 opacity-50">
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide flex items-center gap-1">
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        上一篇
                    </span>
                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">{{ __('No more') }}</p>
                </div>
            @endif

            @if($nextPost)
                <a href="{{ route('blog.show', $nextPost->slug) }}" class="group block p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-200 hover:shadow-md transition-all text-right">
                    <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 uppercase tracking-wide flex items-center justify-end gap-1">
                        下一篇
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                    <p class="mt-1 text-xs md:text-sm font-medium text-gray-900 dark:text-gray-100 truncate md:line-clamp-2 group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ $nextPost->title }}</p>
                </a>
            @else
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 opacity-50 text-right">
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide flex items-center justify-end gap-1">
                        下一篇
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">没有了</p>
                </div>
            @endif
        </div>


        @if($relatedPosts->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                {{ __('Related articles') }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($relatedPosts as $related)
                <a href="{{ route('blog.show', $related->slug) }}" class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-blue-200 transition-all">
                    @if($related->cover_image)
                    <div class="h-32 overflow-hidden">
                        <img src="{{ asset('storage/' . $related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    </div>
                    @endif
                    <div class="p-4">
                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">{{ $related->category->name }}</div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ $related->title }}</h4>
                        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ $related->published_at->format('Y-m-d') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @include('blog.comments_list')
        @include('blog.comment_form')
    </div>

    <!-- 右侧目录 -->
    <div class="hidden lg:block lg:col-span-1">
        <div id="toc-container" class="sticky top-24 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 max-h-[calc(100vh-8rem)] overflow-y-auto">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-3">{{ __('Contents') }}</h3>
            <ul id="toc-list" class="space-y-1 text-sm border-l-2 border-gray-100 dark:border-gray-700 pl-3"></ul>
        </div>

        @if($popularPosts->count() > 0)
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-3">{{ __('Popular articles') }}</h3>
            <ul class="space-y-3">
                @foreach($popularPosts as $pp)
                <li>
                    <a href="{{ route('blog.show', $pp->slug) }}" class="group flex items-start gap-3">
                        @if($pp->cover_image)
                        <img src="{{ asset('storage/' . $pp->cover_image) }}" alt="{{ $pp->title }}" class="w-14 h-14 object-cover rounded-lg flex-shrink-0" loading="lazy">
                        @endif
                        <div class="min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $pp->title }}</h4>
                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">{{ $pp->views ?? 0 }} {{ __('Views') }}</span>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.1.0/dist/medium-zoom.min.js"></script>
<script>
hljs.highlightAll();

(function() {
    // 文章内容图片懒加载
    document.querySelectorAll('.article-content img').forEach(function(img) {
        img.loading = 'lazy';
    });

    const content = document.querySelector('.article-content');
    const headings = content.querySelectorAll('h2, h3');
    const tocContainer = document.getElementById('toc-container');
    const tocList = document.getElementById('toc-list');

    if (headings.length === 0) {
        if (tocContainer) tocContainer.classList.add('hidden');
        return;
    }

    headings.forEach(function(heading, index) {
        const id = 'heading-' + index;
        heading.id = id;

        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = '#' + id;
        a.textContent = heading.textContent;
        a.className = 'block py-1 transition-colors border-l-2 border-transparent -ml-3.5 pl-3 ' +
            (heading.tagName === 'H3' ? 'text-gray-500 hover:text-gray-800 text-xs ml-2' : 'text-gray-600 hover:text-blue-600');
        a.dataset.target = id;

        a.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        li.appendChild(a);
        tocList.appendChild(li);
    });

    const links = tocList.querySelectorAll('a');
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                links.forEach(function(a) {
                    a.classList.remove('text-blue-600', 'font-medium', 'border-blue-500');
                    a.classList.add('border-transparent');
                    if (a.dataset.target === entry.target.id) {
                        a.classList.add('text-blue-600', 'font-medium', 'border-blue-500');
                        a.classList.remove('border-transparent');
                    }
                });
            }
        });
    }, { rootMargin: '-10% 0px -70% 0px', threshold: 0 });

    headings.forEach(function(h) { observer.observe(h); });

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise(function(resolve, reject) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                var ok = document.execCommand('copy');
                document.body.removeChild(textarea);
                ok ? resolve() : reject(new Error('execCommand failed'));
            } catch (e) {
                document.body.removeChild(textarea);
                reject(e);
            }
        });
    }

    document.querySelectorAll('.article-content pre').forEach(function(pre) {
        var codeEl = pre.querySelector('code');
        if (!codeEl) return;
        var button = document.createElement('button');
        button.className = 'copy-code-btn';
        button.textContent = '{{ __('Copy') }}';
        button.addEventListener('click', function() {
            copyText(codeEl.innerText).then(function() {
                button.textContent = '{{ __('Copied') }}';
                setTimeout(function() { button.textContent = '复制'; }, 2000);
            }).catch(function() {
                button.textContent = '{{ __('Failed') }}';
                setTimeout(function() { button.textContent = '复制'; }, 2000);
            });
        });
        pre.appendChild(button);
    });

    // medium-zoom 图片灯箱
    if (typeof mediumZoom !== 'undefined') {
        mediumZoom('.article-content img', {
            background: 'rgba(0,0,0,0.8)',
            margin: 24,
        });
    }

    // 阅读进度条
    (function() {
        var bar = document.getElementById('reading-progress');
        var article = document.querySelector('article');
        if (!bar || !article) return;

        function updateProgress() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var rect = article.getBoundingClientRect();
            var articleTop = rect.top + scrollTop;
            var articleHeight = article.scrollHeight;
            var viewportHeight = window.innerHeight;
            var scrolled = scrollTop - articleTop + viewportHeight;
            var percent = (scrolled / articleHeight) * 100;
            bar.style.width = Math.max(0, Math.min(100, percent)) + '%';
        }

        window.addEventListener('scroll', updateProgress);
        window.addEventListener('resize', updateProgress);
        updateProgress();
    })();

    // 分享功能
    window.shareWechat = function() {
        var url = encodeURIComponent(window.location.href);
        document.getElementById('wechat-qrcode').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + url;
        var modal = document.getElementById('wechat-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.copyLink = function() {
        var url = window.location.href;
        var btn = document.getElementById('copy-text');
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function() {
                btn.textContent = '已复制';
                setTimeout(function() { btn.textContent = '复制链接'; }, 2000);
            });
        } else {
            var textarea = document.createElement('textarea');
            textarea.value = url;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand('copy');
                btn.textContent = '已复制';
                setTimeout(function() { btn.textContent = '复制链接'; }, 2000);
            } catch (e) {}
            document.body.removeChild(textarea);
        }
    };
})();
</script>
@endsection
