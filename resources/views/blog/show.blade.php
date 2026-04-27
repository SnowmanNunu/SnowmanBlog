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
@section('jsonld', json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))

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
.article-content img { border-radius: 0.5rem; margin: 1rem 0; max-width: 100%; }
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- 文章主体 -->
    <div class="lg:col-span-3">
        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            @if($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
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
        </article>

        <!-- 上一篇 / 下一篇 -->
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
                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">没有了</p>
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
                相关文章推荐
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($relatedPosts as $related)
                <a href="{{ route('blog.show', $related->slug) }}" class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-blue-200 transition-all">
                    @if($related->cover_image)
                    <div class="h-32 overflow-hidden">
                        <img src="{{ asset('storage/' . $related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-3">目录</h3>
            <ul id="toc-list" class="space-y-1 text-sm border-l-2 border-gray-100 dark:border-gray-700 pl-3"></ul>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
hljs.highlightAll();

(function() {
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
        button.textContent = '复制';
        button.addEventListener('click', function() {
            copyText(codeEl.innerText).then(function() {
                button.textContent = '已复制';
                setTimeout(function() { button.textContent = '复制'; }, 2000);
            }).catch(function() {
                button.textContent = '失败';
                setTimeout(function() { button.textContent = '复制'; }, 2000);
            });
        });
        pre.appendChild(button);
    });
})();
</script>
@endsection