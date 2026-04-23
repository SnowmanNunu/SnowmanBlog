@extends('layouts.blog')

@section('title', $post->title)

@section('content')
<article class="bg-white rounded-lg shadow p-8">
    @if($post->cover_image)
        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
    @endif

    <h1 class="text-3xl font-bold mb-4">{{ $post->title }}</h1>

    <div class="flex items-center text-sm text-gray-500 mb-6 space-x-4">
        <span>{{ $post->user->name }}</span>
        <span>{{ $post->published_at->format('Y-m-d H:i') }}</span>
        <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600">{{ $post->category->name }}</a>
    </div>

    <div class="prose max-w-none">
        {!! nl2br(e($post->content)) !!}
    </div>

    <div class="mt-8 flex flex-wrap gap-2">
        @foreach($post->tags as $t)
            <a href="{{ route('blog.tag', $t->slug) }}" class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded">{{ $t->name }}</a>
        @endforeach
    </div>
</article>
@endsection