<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteTitle) - {{ $siteDescription }}</title>
    <meta name="description" content="{{ $siteDescription }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen" x-data="{ searchOpen: false }" @keydown.escape.window="searchOpen = false">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="text-xl font-bold text-gray-800">{{ $siteTitle }}</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-gray-900">首页</a>
                    <a href="{{ route('guestbook.index') }}" class="text-gray-600 hover:text-gray-900">留言板</a>
                    <button @click="searchOpen = true" class="text-gray-600 hover:text-gray-900 flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>搜索</span>
                    </button>
                    <a href="/admin" class="text-gray-600 hover:text-gray-900">后台管理</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 搜索弹窗 -->
    <div
        x-show="searchOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] px-4"
        style="display: none;"
    >
        <!-- 背景遮罩 -->
        <div @click="searchOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- 弹窗内容 -->
        <div
            x-show="searchOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
            class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl overflow-hidden"
            style="display: none;"
        >
            <div x-data="searchData()">
                <!-- 搜索输入框 -->
                <div class="border-b border-gray-200 p-4">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            x-model="query"
                            @input.debounce.300ms="performSearch()"
                            placeholder="搜索文章标题、内容..."
                            class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                            x-ref="searchInput"
                        >
                        <button @click="searchOpen = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 搜索结果 -->
                <div class="max-h-[60vh] overflow-y-auto">
                    <!-- 加载中 -->
                    <div x-show="loading" class="p-8 text-center text-gray-500">
                        <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p>搜索中...</p>
                    </div>

                    <!-- 结果列表 -->
                    <template x-if="!loading && results.length > 0">
                        <div>
                            <div class="px-4 py-2 text-xs text-gray-500 bg-gray-50">
                                找到 <span x-text="results.length"></span> 条结果
                            </div>
                            <template x-for="result in results" :key="result.slug">
                                <a :href="'/post/' + result.slug" @click="searchOpen = false"
                                    class="flex items-start space-x-4 p-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0"
                                >
                                    <div x-show="result.cover_image" class="flex-shrink-0">
                                        <img :src="result.cover_image" class="w-20 h-14 object-cover rounded-lg">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-medium text-gray-900 mb-1"
003e<span x-html="result.title"></span></h3>
                                        <p class="text-sm text-gray-600 line-clamp-2"><span x-html="result.excerpt"></span></p>
                                        <div class="flex items-center space-x-3 mt-2 text-xs text-gray-400">
                                            <span x-text="result.published_at"></span>
                                            <span>·</span>
                                            <span x-text="result.category_name"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- 无结果 -->
                    <div x-show="!loading && query.length >= 2 && results.length === 0" class="p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-gray-500">未找到与 "<span x-text="query"></span>" 相关的文章</p>
                        <p class="text-sm text-gray-400 mt-1">换个关键词试试看</p>
                    </div>

                    <!-- 提示 -->
                    <div x-show="query.length === 0" class="p-8 text-center text-gray-400">
                        <p>输入关键词搜索文章...</p>
                        <p class="text-sm mt-1">支持搜索标题、内容、摘要</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.</p>
            @if($siteIcp)
                <p class="mt-2">
                    <a href="https://beian.miit.gov.cn/" target="_blank" class="text-gray-400 hover:text-gray-600">{{ $siteIcp }}</a>
                </p>
            @endif
        </div>
    </footer>

    <script>
        function searchData() {
            return {
                query: '',
                results: [],
                loading: false,
                performSearch() {
                    if (this.query.length < 2) {
                        this.results = [];
                        return;
                    }
                    this.loading = true;
                    fetch('{{ route("blog.search") }}?q=' + encodeURIComponent(this.query))
                        .then(r => r.json())
                        .then(data => {
                            this.results = data;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                        });
                }
            }
        }
    </script>
</body>
</html>