@php
    $approvedComments = $post->comments()->approved()->whereNull('parent_id')->oldest()->get();
@endphp

<div class="mt-12 bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-6">评论 ({{ $post->comments()->approved()->count() }})</h2>

    @if($approvedComments->count() > 0)
        <div class="space-y-6">
            @foreach($approvedComments as $comment)
                <div class="border-b border-gray-100 pb-6 last:border-0">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold flex-shrink-0">
                            {{ mb_substr($comment->nickname, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-medium">{{ $comment->nickname }}</span>
                                <span class="text-sm text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="text-gray-700">{{ $comment->content }}</p>

                            @php
                                $replies = $comment->replies()->approved()->oldest()->get();
                            @endphp
                            @if($replies->count() > 0)
                                <div class="mt-4 space-y-3 pl-4 border-l-2 border-gray-200">
                                    @foreach($replies as $reply)
                                        <div class="bg-gray-50 rounded p-3">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-medium">{{ $reply->nickname }}</span>
                                                <span class="text-sm text-gray-500">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                            <p class="text-gray-700">{{ $reply->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <button onclick="toggleReplyForm({{ $comment->id }})" class="text-sm text-blue-600 hover:underline mt-2">回复</button>

                            <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 bg-gray-50 p-4 rounded">
                                <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <input type="text" name="nickname" required placeholder="昵称 *" class="rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="email" name="email" placeholder="邮箱" class="rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="url" name="website" placeholder="网站" class="rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <textarea name="content" rows="3" required placeholder="回复内容 *" class="w-full rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">提交回复</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 py-8">暂无评论，来说两句吧！</div>
    @endif
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById('reply-form-' + id);
    form.classList.toggle('hidden');
}
</script>