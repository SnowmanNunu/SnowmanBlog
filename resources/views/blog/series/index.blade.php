@extends('layouts.blog')

@section('title', '专栏')
@section('meta_description', '浏览所有专栏系列文章')
@section('canonical', route('series.index'))

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 text-center dark:text-gray-100">专栏</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($series as $s)
        <a href="{{ route('series.show', $s->slug) }}" class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-blue-200 transition-all">
            @if($s->cover_image)
            <div class="h-40 w-full overflow-hidden">
                <img src="{{ media_url($s->cover_image) }}" alt="{{ $s->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
            </div>
            @endif
            <div class="p-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $s->name }}</h2>
                @if($s->description)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">{{ $s->description }}</p>
                @endif
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $s->published_posts_count }} 篇文章</span>
                    <span class="text-sm text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform">查看全部 →</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-gray-500 dark:text-gray-400">暂无专栏</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
