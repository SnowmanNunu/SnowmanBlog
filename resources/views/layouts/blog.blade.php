<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SnowmanBlog') - 我的个人博客</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="text-xl font-bold text-gray-800">SnowmanBlog</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-gray-900">首页</a>
                    <a href="/admin" class="text-gray-600 hover:text-gray-900">后台管理</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-500">
            &copy; {{ date('Y') }} SnowmanBlog. All rights reserved.
        </div>
    </footer>
</body>
</html>