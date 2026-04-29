<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">最新待审内容</x-slot>

        <div
            x-data="{
                open: false,
                action: '',
                actionLabel: '',
                actionColor: '',
                reviewId: null,
                reviewType: '',
                reviewContent: '',
                openModal(type, id, reviewType, content) {
                    this.action = type;
                    this.reviewId = id;
                    this.reviewType = reviewType;
                    this.reviewContent = content;
                    if (type === 'approve') {
                        this.actionLabel = '确认通过';
                        this.actionColor = 'success';
                    } else {
                        this.actionLabel = '确认删除';
                        this.actionColor = 'danger';
                    }
                    this.open = true;
                },
                confirm() {
                    if (this.action === 'approve') {
                        $wire.approve(this.reviewId, this.reviewType);
                    } else {
                        $wire.deleteReview(this.reviewId, this.reviewType);
                    }
                    this.open = false;
                }
            }"
            @keydown.escape.window="open = false"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">类型</th>
                            <th class="px-4 py-3">昵称</th>
                            <th class="px-4 py-3">内容</th>
                            <th class="px-4 py-3">关联文章</th>
                            <th class="px-4 py-3">提交时间</th>
                            <th class="px-4 py-3 text-right">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->getPendingReviews() as $review)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3">
                                    @if ($review->type === '评论')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400">评论</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-50 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400">留言</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $review->nickname }}</td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $review->content }}">{{ $review->content }}</td>
                                <td class="px-4 py-3">
                                    @php $postTitle = $this->getPostTitle($review->post_id); @endphp
                                    @if ($postTitle)
                                        <span class="text-gray-500 dark:text-gray-400 truncate max-w-[150px] inline-block">{{ $postTitle }}</span>
                                    @else
                                        <span class="text-gray-400">留言板</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($review->created_at)->format('m-d H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="openModal('approve', {{ $review->id }}, '{{ $review->type }}', '{{ addslashes($review->content) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-success-50 text-success-600 hover:bg-success-100 dark:bg-success-400/10 dark:text-success-400 dark:hover:bg-success-400/20 transition-colors"
                                            title="通过"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click="openModal('delete', {{ $review->id }}, '{{ $review->type }}', '{{ addslashes($review->content) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-danger-50 text-danger-600 hover:bg-danger-100 dark:bg-danger-400/10 dark:text-danger-400 dark:hover:bg-danger-400/20 transition-colors"
                                            title="删除"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    暂无待审内容
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal --}}
            <div
                x-show="open"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="display: none;"
            >
                {{-- Backdrop --}}
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-gray-950/50 dark:bg-gray-950/70"
                    @click="open = false"
                ></div>

                {{-- Dialog --}}
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                >
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="actionLabel"></h3>
                    </div>

                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            确定要<span x-text="action === 'approve' ? '通过' : '删除'"></span>这条 <span class="font-medium text-gray-700 dark:text-gray-300" x-text="reviewType"></span> 吗？
                        </p>
                        <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300 truncate" x-text="reviewContent"></p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="open = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            取消
                        </button>
                        <button
                            type="button"
                            x-show="action === 'approve'"
                            @click="confirm()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-green-600 hover:bg-green-500"
                        >
                            确认通过
                        </button>
                        <button
                            type="button"
                            x-show="action === 'delete'"
                            @click="confirm()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-red-600 hover:bg-red-500"
                        >
                            确认删除
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
