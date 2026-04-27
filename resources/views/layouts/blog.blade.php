<!DOCTYPE html>
<html lang="zh-CN" class="dark" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light')); if (!darkMode) document.documentElement.classList.remove('dark')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteTitle . ' - ' . $siteDescription)</title>
    <meta name="description" content="@yield('meta_description', $siteDescription)">
    <meta name="keywords" content="@yield('meta_keywords', '')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', $siteTitle . ' - ' . $siteDescription)">
    <meta property="og:description" content="@yield('og_description', $siteDescription)">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteTitle }}">
    @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">

    <!-- Structured Data -->
    @hasSection('jsonld')
    <script type="application/ld+json">
    @yield('jsonld')
    </script>
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex flex-col min-h-screen" x-data="{ searchOpen: false, mobileMenuOpen: false }" @keydown.escape.window="searchOpen = false; mobileMenuOpen = false">
    <!-- 顶部导航 -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent hover:opacity-80 transition-opacity">
                        {{ $siteTitle }}
                    </a>
                </div>

                <!-- 桌面端导航 -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('blog.index') }}" class="px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-sm font-medium">
                        首页
                    </a>
                    <a href="{{ route('guestbook.index') }}" class="px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-sm font-medium">
                        留言板
                    </a>
                    <a href="/admin" class="px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-sm font-medium">
                        后台管理
                    </a>
                    <div class="w-px h-5 bg-gray-300 mx-2"></div>
                    <button @click="searchOpen = true; setTimeout(() => $refs.searchInput && $refs.searchInput.focus(), 100)"
                        class="flex items-center space-x-2 px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all text-sm border border-gray-200 dark:border-gray-700 hover:border-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>{{ __('Search articles...') }}</span>
                        <kbd class="hidden lg:inline-block px-1.5 py-0.5 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded text-gray-400 dark:text-gray-500">Ctrl K</kbd>
                    </button>
                    <div class="flex items-center space-x-1 px-2">
                    <a href="{{ url()->current() }}?lang=zh_CN" class="text-xs {{ app()->getLocale() === 'zh_CN' ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">{{ __('Chinese') }}</a>
                    <span class="text-gray-300 dark:text-gray-600">|</span>
                    <a href="{{ url()->current() }}?lang=en" class="text-xs {{ app()->getLocale() === 'en' ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">{{ __('English') }}</a>
                </div>
                    <button id="theme-toggle" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="{{ __('Toggle theme') }}">
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </button>
                    <script>
                        document.getElementById('theme-toggle').addEventListener('click', function() {
                            var html = document.documentElement;
                            html.classList.toggle('dark');
                            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
                        });
                    </script>
                </div>

                <!-- 移动端菜单按钮 -->
                <div class="flex md:hidden items-center space-x-2">
                    <button id="theme-toggle-mobile" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="切换主题">
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </button>
                    <script>
                        (function(){
                            var btn = document.getElementById('theme-toggle-mobile');
                            if(btn) btn.addEventListener('click', function(){
                                var html = document.documentElement;
                                html.classList.toggle('dark');
                                localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
                            });
                        })();
                    </script>
                    <button @click="searchOpen = true" class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- 移动端菜单 -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 space-y-1 shadow-lg"
    >
        <a href="{{ route('blog.index') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 font-medium">{{ __('Home') }}</a>
        <a href="{{ route('guestbook.index') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 font-medium">{{ __('Guestbook') }}</a>
        <a href="/admin" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 font-medium">{{ __('Admin') }}</a>
        <button @click="mobileMenuOpen = false; searchOpen = true" class="w-full text-left px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 font-medium flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>{{ __('Search') }}</span>
        </button>
    </div>

    <!-- 搜索弹窗 -->
    <div
        x-show="searchOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] px-4"
    >
        <!-- 背景遮罩 -->
        <div @click="searchOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <!-- 弹窗内容 -->
        <div
            x-show="searchOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
            class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5 dark:ring-gray-700/50"
        >
            <div x-data="searchData()" @search-opened.window="$nextTick(() => $refs.searchInput && $refs.searchInput.focus())">
                <!-- 搜索输入框 -->
                <div class="border-b border-gray-100 dark:border-gray-700 p-4">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            x-model="query"
                            @input.debounce.300ms="performSearch()"
                            placeholder="搜索文章标题、内容..."
                            class="w-full pl-12 pr-12 py-3.5 rounded-xl border-0 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-base transition-all"
                            x-ref="searchInput"
                        >
                        <button @click="searchOpen = false" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 搜索结果 -->
                <div class="max-h-[60vh] overflow-y-auto">
                    <!-- 加载中 -->
                    <div x-show="loading" class="p-10 text-center">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 mb-3">
                            <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">搜索中...</p>
                    </div>

                    <!-- 结果列表 -->
                    <template x-if="!loading && results.length > 0">
                        <div>
                            <div class="px-5 py-2.5 text-xs text-gray-500 dark:text-gray-400 bg-gray-50/80 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span>找到 <span x-text="results.length" class="font-medium text-gray-700 dark:text-gray-300"></span> 条结果</span>
                            </div>
                            <template x-for="result in results" :key="result.slug">
                                <a :href="'/post/' + result.slug" @click="searchOpen = false"
                                    class="flex items-start space-x-4 p-4 hover:bg-blue-50/50 transition-colors border-b border-gray-50 last:border-0 group"
                                >
                                    <div x-show="result.cover_image" class="flex-shrink-0">
                                        <img :src="result.cover_image" class="w-20 h-14 object-cover rounded-lg shadow-sm group-hover:shadow transition-shadow">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug">
                                            <span x-html="result.title"></span>
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                            <span x-html="result.excerpt"></span>
                                        </p>
                                        <div class="flex items-center space-x-2 mt-2 text-xs text-gray-400 dark:text-gray-500">
                                            <span x-text="result.published_at" class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full"></span>
                                            <span class="text-gray-300 dark:text-gray-600">·</span>
                                            <span x-text="result.category_name" class="text-gray-500 dark:text-gray-400"></span>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 dark:text-gray-600 group-hover:text-blue-400 transition-colors mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- 无结果 -->
                    <div x-show="!loading && query.length >= 2 && results.length === 0" class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">未找到与 "<span x-text="query" class="text-gray-700 dark:text-gray-300"></span>" 相关的文章</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">换个关键词试试看</p>
                    </div>

                    <!-- 提示 -->
                    <div x-show="query.length === 0" class="p-10 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/20 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">输入关键词搜索文章</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">支持搜索标题、内容、摘要</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        @yield('content')
    </main>

    <footer class="bg-white dark:bg-gray-800 border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <p class="text-gray-600 dark:text-gray-400 font-medium">{{ $siteTitle }}</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">{{ $siteDescription }}</p>
                    @if(isset($links) && $links->isNotEmpty())
                        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                            <span class="text-gray-400 dark:text-gray-500">友情链接：</span>
                            @foreach($links as $link)
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ $link->description }}">{{ $link->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="text-center md:text-right text-sm text-gray-400 dark:text-gray-500">
                    <p>&copy; {{ date('Y') }} {{ $siteTitle }}. {{ __('All rights reserved') }}</p>
                    @if($siteIcp)
                        <p class="mt-1">
                            <a href="https://beian.miit.gov.cn/" target="_blank" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">{{ $siteIcp }}</a>
                        </p>
                    @endif
                </div>
            </div>
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

        // Ctrl+K / Cmd+K 快捷键
        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                document.body.dispatchEvent(new CustomEvent('search-opened'));
                const alpineData = document.querySelector('[x-data*="searchOpen"]')._x_dataStack[0];
                if (alpineData) alpineData.searchOpen = true;
            }
        });
    </script>
</body>
</html>