@extends('layouts.blog')

@section('title', isset($category) ? $category->name : (isset($tag) ? $tag->name : __('Home')))

@section('jsonld')
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "{{ $siteTitle }}",
    "description": "{{ $siteDescription }}",
    "url": "{{ url('/') }}",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ url('/search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
@endsection

@section('content')
<div class="space-y-8">
    @if(isset($category))
        <h1 class="text-2xl font-bold dark:text-gray-100">{{ __('Category') }}：{{ $category->name }}</h1>
    @elseif(isset($tag))
        <h1 class="text-2xl font-bold dark:text-gray-100">{{ __('Tag') }}：{{ $tag->name }}</h1>
    @endif

    @forelse($posts as $post)
        <article class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            @if($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded-lg mb-4" loading="lazy">
            @endif
            <h2 class="text-xl font-bold mb-2 flex items-center gap-2">
                @if($post->is_pinned)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ __('Pinned') }}</span>
                @endif
                <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">{{ $post->title }}</a>
            </h2>
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3 space-x-4">
                <span>{{ $post->user->name }}</span>
                <span>{{ $post->published_at->format('Y-m-d') }}</span>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600 dark:text-blue-400">{{ $post->category->name }}</a>
                <span class="flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ $post->views ?? 0 }}</span>
                </span>
                <span class="flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $post->likes_count ?? 0 }}</span>
                </span>
            </div>
            @if($post->excerpt)
                <p class="text-gray-600 dark:text-gray-300">{{ $post->excerpt }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($post->tags as $t)
                    <a href="{{ route('blog.tag', $t->slug) }}" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">{{ $t->name }}</a>
                @endforeach
            </div>
        </article>
    @empty
        <p class="text-gray-500 dark:text-gray-400">{{ __('No articles yet') }}</p>
    @endforelse

    {{ $posts->links() }}

    @if(!isset($category) && !isset($tag))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">开源项目</h3>
        <div class="flex flex-wrap gap-4">
            <a href="https://gitee.com/SnowmanNunu/snowman-blog" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-600">
                <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.984 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.016 0zm6.09 5.333c.328 0 .593.266.592.593v1.482a.594.594 0 0 1-.593.592H9.777c-.982 0-1.778.796-1.778 1.778v5.63c0 .327.266.592.593.592h5.63c.982 0 1.778-.796 1.778-1.778v-.296a.593.593 0 0 0-.592-.593h-4.15a.592.592 0 0 1-.592-.592v-1.482a.593.593 0 0 1 .593-.592h6.815c.327 0 .593.265.593.592v3.408a4 4 0 0 1-4 4H5.926a.593.593 0 0 1-.593-.593V9.778a4.444 4.444 0 0 1 4.445-4.444h8.296Z"/></svg>
                <span class="font-medium">Gitee</span>
            </a>
            <a href="https://github.com/SnowmanNunu/SnowmanBlog" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-600">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.233 1.91 1.233 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                <span class="font-medium">GitHub</span>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
