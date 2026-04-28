@php
    $approvedComments = $post->comments()->approved()->whereNull('parent_id')->oldest()->get();
@endphp

<div class="mt-12 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-6">评论 ({{ $post->comments()->approved()->count() }})</h2>

    @if($approvedComments->count() > 0)
        <div class="space-y-6">
            @foreach($approvedComments as $comment)
                <div class="border-b border-gray-100 dark:border-gray-700 pb-6 last:border-0">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-400 font-bold flex-shrink-0">
                            {{ mb_substr($comment->nickname, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-medium">{{ $comment->nickname }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="comment-markdown text-gray-700 dark:text-gray-300">{!! clean(Str::markdown($comment->content)) !!}</div>

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

                            @php
                                $replies = $comment->replies()->approved()->oldest()->get();
                            @endphp
                            @if($replies->count() > 0)
                                <div class="mt-4 space-y-3 pl-4 border-l-2 border-gray-200 dark:border-gray-700">
                                    @foreach($replies as $reply)
                                        <div class="bg-gray-50 dark:bg-gray-900 rounded p-3">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-medium">{{ $reply->nickname }}</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                            <div class="comment-markdown text-gray-700 dark:text-gray-300">{!! clean(Str::markdown($reply->content)) !!}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <button onclick="toggleReplyForm({{ $comment->id }})" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2">回复</button>

                            <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 bg-gray-50 dark:bg-gray-900 p-4 rounded">
                                <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <input type="text" name="nickname" required placeholder="昵称 *" class="rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="email" name="email" placeholder="邮箱" class="rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="url" name="website" placeholder="网站" class="rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <textarea name="content" rows="3" required placeholder="回复内容 *" class="w-full rounded border-gray-300 dark:border-gray-600 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">提交回复</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">暂无评论，来说两句吧！</div>
    @endif
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById('reply-form-' + id);
    form.classList.toggle('hidden');
}
</script>