@extends('layouts.blog')

@section('title', isset($category) ? $category->name : (isset($tag) ? $tag->name : '首页'))

@section('content')
<div class="space-y-8">
    @if(isset($category))
        <h1 class="text-2xl font-bold">分类：{{ $category->name }}</h1>
    @elseif(isset($tag))
        <h1 class="text-2xl font-bold">标签：{{ $tag->name }}</h1>
    @endif

    @forelse($posts as $post)
        <article class="bg-white rounded-lg shadow p-6">
            @if($post->cover_image)
                <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded-lg mb-4">
            @endif
            <h2 class="text-xl font-bold mb-2">
                <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-900 hover:text-blue-600">{{ $post->title }}</a>
            </h2>
            <div class="flex items-center text-sm text-gray-500 mb-3 space-x-4">
                <span>{{ $post->user->name }}</span>
                <span>{{ $post->published_at->format('Y-m-d') }}</span>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600">{{ $post->category->name }}</a>
            </div>
            @if($post->excerpt)
                <p class="text-gray-600">{{ $post->excerpt }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($post->tags as $t)
                    <a href="{{ route('blog.tag', $t->slug) }}" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">{{ $t->name }}</a>
                @endforeach
            </div>
        </article>
    @empty
        <p class="text-gray-500">暂无文章</p>
    @endforelse

    {{ $posts->links() }}
</div>
@endsection