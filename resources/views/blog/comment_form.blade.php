<div class="mt-12 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">发表评论</h2>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-4" onsubmit="document.getElementById('comment-submit').disabled=true;this.submit();">
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
        <button id="comment-submit" type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">发表评论</button>
    </form>
</div>
