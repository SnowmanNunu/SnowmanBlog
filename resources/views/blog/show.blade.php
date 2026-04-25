@extends('layouts.blog')

@section('title', $post->title)

@section('content')
<style>
.article-content h2 { font-size: 1.5rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #111827; }
.article-content h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #1f2937; }
.article-content p { margin-bottom: 1rem; line-height: 1.75; }
.article-content ul, .article-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
.article-content ul { list-style-type: disc; }
.article-content ol { list-style-type: decimal; }
.article-content blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; color: #6b7280; font-style: italic; margin-bottom: 1rem; }
.article-content pre { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 1rem; }
.article-content pre code { background: transparent; padding: 0; }
.article-content code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.875rem; background: #f1f5f9; padding: 0.125rem 0.375rem; border-radius: 0.25rem; color: #ef4444; }
.article-content hr { border: 0; border-top: 1px solid #e5e7eb; margin: 1.5rem 0; }
.article-content a { color: #2563eb; text-decoration: underline; }
.article-content img { border-radius: 0.5rem; margin: 1rem 0; max-width: 100%; }
.article-content table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
.article-content th, .article-content td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; }
.article-content th { background: #f9fafb; font-weight: 600; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- 文章主体 -->
    <div class="lg:col-span-3">
        <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @if($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
            @endif

            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

            <div class="flex items-center text-sm text-gray-500 mb-8 space-x-4">
                <span class="font-medium text-gray-700">{{ $post->user->name }}</span>
                <span class="text-gray-300">·</span>
                <span>{{ $post->published_at->format('Y-m-d H:i') }}</span>
                <span class="text-gray-300">·</span>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium">{{ $post->category->name }}</a>
            </div>

            <div class="article-content max-w-none text-gray-700 leading-relaxed">
                {!! Str::markdown($post->content) !!}
            </div>

            <div class="mt-10 flex flex-wrap gap-2">
                @foreach($post->tags as $t)
                    <a href="{{ route('blog.tag', $t->slug) }}" class="px-3 py-1 bg-blue-50 text-blue-600 text-sm rounded-full hover:bg-blue-100 transition-colors">{{ $t->name }}</a>
                @endforeach
            </div>
        </article>

        @include('blog.comments_list')
        @include('blog.comment_form')
    </div>

    <!-- 右侧目录 -->
    <div class="hidden lg:block lg:col-span-1">
        <div id="toc-container" class="sticky top-24 bg-white rounded-xl shadow-sm border border-gray-100 p-5 max-h-[calc(100vh-8rem)] overflow-y-auto">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">目录</h3>
            <ul id="toc-list" class="space-y-1 text-sm border-l-2 border-gray-100 pl-3"></ul>
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
})();
</script>
@endsection