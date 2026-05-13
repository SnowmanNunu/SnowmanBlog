@extends('layouts.blog')

@section('title', $series->name)
@section('meta_description', $series->description ? strip_tags($series->description) : $series->name)
@section('canonical', route('series.show', $series->slug))

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- 专栏头部 -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        @if($series->cover_image)
        <div class="h-48 md:h-64 w-full relative">
            <img src="{{ media_url($series->cover_image) }}" alt="{{ $series->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $series->name }}</h1>
                @if($series->description)
                <p class="text-white/80 text-sm md:text-base max-w-2xl">{{ $series->description }}</p>
                @endif
            </div>
        </div>
        @else
        <div class="p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $series->name }}</h1>
            @if($series->description)
            <p class="text-gray-500 dark:text-gray-400 text-sm md:text-base max-w-2xl">{{ $series->description }}</p>
            @endif
        </div>
        @endif
    </div>

    <!-- 文章列表 -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3 space-y-6">
            @forelse($posts as $post)
            <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                <a href="{{ route('blog.show', $post->slug) }}" class="block md:flex">
                    @if($post->cover_image)
                    <div class="md:w-48 md:flex-shrink-0 h-48 md:h-auto">
                        <img src="{{ media_url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    @endif
                    <div class="p-5 flex flex-col justify-between flex-1">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">{{ $post->category->name }}</span>
                                <span class="text-gray-300 dark:text-gray-600">·</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $post->published_at->format('Y-m-d') }}</span>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $post->title }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $post->excerpt }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ $post->views ?? 0 }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                {{ $post->likes_count ?? 0 }}
                            </span>
                        </div>
                    </div>
                </a>
            </article>
            @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400">该专栏暂无文章</p>
            </div>
            @endforelse

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- 侧边栏 -->
        <div class="hidden lg:block lg:col-span-1">
            @include('components.subscribe-form')
        </div>
    </div>
</div>
@endsection
