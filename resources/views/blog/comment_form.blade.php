<div class="mt-12 bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">发表评论</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">昵称 *</label>
                <input type="text" name="nickname" required class="w-full rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
                <input type="email" name="email" class="w-full rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">网站</label>
                <input type="url" name="website" placeholder="https://example.com" class="w-full rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">内容 *</label>
            <textarea name="content" rows="4" required class="w-full rounded border-gray-300 px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">发表评论</button>
    </form>
</div>