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
</div>
@endsection
