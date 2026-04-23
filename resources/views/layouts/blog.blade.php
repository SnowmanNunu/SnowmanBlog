<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteTitle) - {{ $siteDescription }}</title>
    <meta name="description" content="{{ $siteDescription }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="text-xl font-bold text-gray-800">{{ $siteTitle }}</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-gray-900">首页</a>
                    <a href="{{ route('guestbook.index') }}" class="text-gray-600 hover:text-gray-900">留言板</a>
                    <a href="/admin" class="text-gray-600 hover:text-gray-900">后台管理</a>
                </div>
            </div>
        </div>
    </nav>

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
</body>
</html>