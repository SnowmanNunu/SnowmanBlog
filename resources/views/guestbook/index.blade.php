@extends('layouts.blog')

@section('title', '留言板')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 text-center">留言板</h1>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">发表留言</h2>
        <form action="{{ route('guestbook.store') }}" method="POST" class="space-y-4" onsubmit="document.getElementById('guestbook-submit').disabled=true;this.submit();">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">昵称 *</label>
                    <input type="text" name="nickname" required class="w-full rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">邮箱</label>
                    <input type="email" name="email" class="w-full rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">网站</label>
                    <input type="url" name="website" placeholder="https://example.com" class="w-full rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">内容 *</label>
                <textarea name="content" rows="4" required class="w-full rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">支持 Markdown 语法和 Emoji 😀</p>
            </div>
            <button id="guestbook-submit" type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">提交留言</button>
        </form>
    </div>

    <div class="space-y-6">
        @forelse($messages as $msg)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                            {{ mb_substr($msg->nickname, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium">
                                @if($msg->website)
                                    <a href="{{ $msg->website }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $msg->nickname }}</a>
                                @else
                                    {{ $msg->nickname }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $msg->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="comment-markdown text-gray-700 dark:text-gray-300">{!! clean(Str::markdown($msg->content)) !!}</div>

                <style>
                .comment-markdown p { margin-bottom: 0.5rem; line-height: 1.6; }
                .comment-markdown p:last-child { margin-bottom: 0; }
                .comment-markdown ul, .comment-markdown ol { margin-bottom: 0.5rem; padding-left: 1.25rem; }
                .comment-markdown ul { list-style-type: disc; }
                .comment-markdown ol { list-style-type: decimal; }
                .comment-markdown code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.875em; background: #f1f5f9; padding: 0.125rem 0.375rem; border-radius: 0.25rem; color: #ef4444; }
                .dark .comment-markdown code { background: #1f2937; color: #f87171; }
                .comment-markdown pre { background: #1e293b; color: #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 0.5rem; }
                .comment-markdown pre code { background: transparent; padding: 0; color: #e2e8f0; }
                .comment-markdown blockquote { border-left: 3px solid #e5e7eb; padding-left: 0.75rem; color: #6b7280; font-style: italic; margin-bottom: 0.5rem; }
                .dark .comment-markdown blockquote { border-left-color: #374151; color: #9ca3af; }
                .comment-markdown a { color: #2563eb; text-decoration: underline; }
                .dark .comment-markdown a { color: #3b82f6; }
                .comment-markdown strong { font-weight: 600; }
                .comment-markdown h1, .comment-markdown h2, .comment-markdown h3, .comment-markdown h4 { font-weight: 600; margin-top: 0.75rem; margin-bottom: 0.25rem; }
                </style>

                @if($msg->isReplied())
                    <div class="mt-4 bg-green-50 dark:bg-green-900/10 border-l-4 border-green-400 dark:border-green-700 p-4 rounded">
                        <div class="flex items-center mb-2">
                            <span class="bg-green-600 text-white text-xs px-2 py-1 rounded">博主回复</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $msg->replied_at?->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300">{{ $msg->reply }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 py-12">还没有留言，来抢沙发吧！</div>
        @endforelse

        {{ $messages->links() }}
    </div>
</div>
@endsection
