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
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3 space-y-8">
        @if(isset($categories) && $categories->count())
        <div class="lg:hidden flex overflow-x-auto gap-2 pb-2">
            @foreach($categories as $cat)
                <a href="{{ route('blog.category', $cat->slug) }}" class="flex-shrink-0 px-3 py-1.5 bg-white dark:bg-gray-800 rounded-full border border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                    {{ $cat->name }} <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">{{ $cat->posts_count }}</span>
                </a>
            @endforeach
        </div>
        @endif

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
    </div>

    <div class="hidden lg:block lg:col-span-1">
        <div class="sticky top-24 space-y-6">
            @if(isset($categories) && $categories->count())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    {{ __('Categories') }}
                </h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}" class="flex items-center justify-between px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group">
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $cat->name }}</span>
                            <span class="text-xs font-medium bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full border border-gray-100 dark:border-gray-600 group-hover:border-blue-200 dark:group-hover:border-blue-800 transition-colors">{{ $cat->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($tags) && $tags->count())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    {{ __('Popular tags') }}
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $t)
                        <a href="{{ route('blog.tag', $t->slug) }}" class="px-3 py-1.5 bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-xs rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors border border-transparent hover:border-blue-100 dark:hover:border-blue-800">
                            {{ $t->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($popularPosts) && $popularPosts->count())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                    {{ __('Popular articles') }}
                </h3>
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
</div>
@endsection
