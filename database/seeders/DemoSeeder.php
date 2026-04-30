<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Guestbook;
use App\Models\Link;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdmin();
        $this->createCategories();
        $this->createTags();
        $this->createPosts();
        $this->createComments();
        $this->createGuestbooks();
        $this->createLinks();
    }

    private function createAdmin(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'SnowmanNunu',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    private function createCategories(): void
    {
        $categories = [
            ['name' => '技术分享', 'slug' => 'tech', 'description' => '编程语言、框架与工具的深度解析'],
            ['name' => '开源项目', 'slug' => 'open-source', 'description' => '开源工具推荐与使用心得'],
            ['name' => '生活随笔', 'slug' => 'life', 'description' => '日常思考与生活记录'],
            ['name' => '学习笔记', 'slug' => 'notes', 'description' => '技术学习过程中的笔记整理'],
            ['name' => '工具推荐', 'slug' => 'tools', 'description' => '提升效率的开发工具与软件'],
            ['name' => '最佳实践', 'slug' => 'best-practices', 'description' => '代码规范与工程化实践'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }

    private function createTags(): void
    {
        $tags = [
            'Laravel', 'PHP', 'Vue.js', 'React', 'Tailwind CSS',
            'Docker', 'MySQL', 'Redis', 'Git', 'Linux',
            'Nginx', 'API设计', '微服务', '性能优化', '测试驱动开发',
            'CI/CD', 'TypeScript', '前后端分离', 'Serverless', 'GraphQL',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }

    private function createPosts(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $categories = Category::all();
        $tags = Tag::all();

        $posts = [
            [
                'title' => 'Laravel 12 新特性一览：更快、更简洁的现代化框架',
                'excerpt' => 'Laravel 12 带来了众多令人兴奋的更新，包括性能优化、更简洁的语法以及全新的脚手架工具。本文将逐一解析这些变化。',
                'content' => $this->articleContent('Laravel 12'),
                'is_pinned' => true,
                'category_id' => $categories->firstWhere('slug', 'tech')?->id,
                'tag_slugs' => ['Laravel', 'PHP'],
            ],
            [
                'title' => '使用 Filament v3 快速构建 Laravel 后台管理系统',
                'excerpt' => 'Filament v3 是目前 Laravel 生态中最强大的后台面板解决方案之一。本文将通过实际案例演示如何用它构建一个功能完备的管理后台。',
                'content' => $this->articleContent('Filament'),
                'is_pinned' => true,
                'category_id' => $categories->firstWhere('slug', 'tech')?->id,
                'tag_slugs' => ['Laravel', 'PHP'],
            ],
            [
                'title' => 'Docker 多阶段构建优化 PHP 应用镜像体积',
                'excerpt' => '生产环境的 Docker 镜像往往体积臃肿，本文介绍如何利用多阶段构建将 PHP 应用镜像从 500MB 压缩到 80MB。',
                'content' => $this->articleContent('Docker'),
                'category_id' => $categories->firstWhere('slug', 'best-practices')?->id,
                'tag_slugs' => ['Docker', 'Linux', '性能优化'],
            ],
            [
                'title' => 'MySQL 索引优化实战：从慢查询到毫秒响应',
                'excerpt' => '慢查询是性能瓶颈的常见来源。本文通过真实案例，讲解如何分析执行计划、设计复合索引以及避免常见的索引误区。',
                'content' => $this->articleContent('MySQL'),
                'category_id' => $categories->firstWhere('slug', 'best-practices')?->id,
                'tag_slugs' => ['MySQL', '性能优化'],
            ],
            [
                'title' => 'Tailwind CSS v4 升级指南：JIT 引擎的彻底重构',
                'excerpt' => 'Tailwind CSS v4 采用了全新的基于 Rust 的引擎，编译速度提升 10 倍。本文记录了升级过程中的注意事项和 breaking changes。',
                'content' => $this->articleContent('Tailwind CSS'),
                'category_id' => $categories->firstWhere('slug', 'tech')?->id,
                'tag_slugs' => ['Tailwind CSS', '前端工程化'],
            ],
            [
                'title' => 'Redis 缓存策略在 Laravel 中的最佳实践',
                'excerpt' => '缓存是提升应用性能的核心手段。本文对比了 Laravel 中多种缓存驱动和策略，并给出可落地的缓存架构设计建议。',
                'content' => $this->articleContent('Redis'),
                'category_id' => $categories->firstWhere('slug', 'best-practices')?->id,
                'tag_slugs' => ['Redis', 'Laravel', '性能优化'],
            ],
            [
                'title' => '从零搭建个人博客：技术选型与架构设计',
                'excerpt' => '记录我搭建 SnowmanBlog 的全过程，包括为什么选择 Laravel + Filament，以及如何平衡功能丰富度和代码简洁度。',
                'content' => $this->articleContent('博客搭建'),
                'category_id' => $categories->firstWhere('slug', 'life')?->id,
                'tag_slugs' => ['Laravel', 'Vue.js', '前后端分离'],
            ],
            [
                'title' => 'TypeScript 高级类型体操：从入门到实践',
                'excerpt' => '泛型、条件类型、映射类型... TypeScript 的类型系统强大但复杂。本文用实际场景带你理解这些高级特性。',
                'content' => $this->articleContent('TypeScript'),
                'category_id' => $categories->firstWhere('slug', 'notes')?->id,
                'tag_slugs' => ['TypeScript'],
            ],
            [
                'title' => 'Git 工作流规范：团队协作中的分支策略',
                'excerpt' => 'Git Flow、GitHub Flow、Trunk-based... 哪种工作流适合你的团队？本文对比分析并给出具体操作建议。',
                'content' => $this->articleContent('Git'),
                'category_id' => $categories->firstWhere('slug', 'best-practices')?->id,
                'tag_slugs' => ['Git', 'CI/CD'],
            ],
            [
                'title' => 'Serverless 架构实践：用 Laravel Vapor 部署无服务应用',
                'excerpt' => '无服务器架构正在改变后端开发的范式。本文介绍如何使用 Laravel Vapor 将传统应用平滑迁移到 AWS Lambda。',
                'content' => $this->articleContent('Serverless'),
                'category_id' => $categories->firstWhere('slug', 'tech')?->id,
                'tag_slugs' => ['Serverless', 'Laravel', 'API设计'],
            ],
            [
                'title' => 'Nginx 性能调优：从配置到内核参数',
                'excerpt' => 'Nginx 默认配置并不适合高并发场景。本文整理了从 worker 进程数、缓存策略到 Linux 内核参数的全方位调优方案。',
                'content' => $this->articleContent('Nginx'),
                'category_id' => $categories->firstWhere('slug', 'tools')?->id,
                'tag_slugs' => ['Nginx', 'Linux', '性能优化'],
            ],
            [
                'title' => 'API 版本控制与向后兼容的设计哲学',
                'excerpt' => 'API 一旦发布，变更成本极高。本文探讨 RESTful API 的版本控制策略，以及如何在演进中保持向后兼容。',
                'content' => $this->articleContent('API设计'),
                'category_id' => $categories->firstWhere('slug', 'best-practices')?->id,
                'tag_slugs' => ['API设计', '前后端分离'],
            ],
        ];

        foreach ($posts as $index => $data) {
            $post = Post::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'category_id' => $data['category_id'] ?? $categories->random()->id,
                    'user_id' => $admin->id,
                    'is_published' => true,
                    'is_pinned' => $data['is_pinned'] ?? false,
                    'published_at' => now()->subDays($index + 1),
                    'meta_title' => $data['title'],
                    'meta_description' => $data['excerpt'],
                    'meta_keywords' => implode(', ', $data['tag_slugs'] ?? []),
                ]
            );

            $postTagIds = $tags->whereIn('name', $data['tag_slugs'] ?? [])->pluck('id');
            if ($postTagIds->isNotEmpty()) {
                $post->tags()->sync($postTagIds);
            }
        }

        // Create a draft post
        Post::firstOrCreate(
            ['slug' => 'draft-post-example'],
            [
                'title' => '【草稿】GraphQL 与 REST 的取舍之道',
                'excerpt' => '这是一篇尚未发布的草稿文章，用于演示草稿功能。',
                'content' => $this->articleContent('GraphQL'),
                'category_id' => $categories->random()->id,
                'user_id' => $admin->id,
                'is_published' => false,
                'is_pinned' => false,
                'published_at' => null,
                'meta_title' => 'GraphQL 与 REST 的取舍之道',
                'meta_description' => 'GraphQL 和 REST 各有优劣，本文从实际项目出发分析适用场景。',
                'meta_keywords' => 'GraphQL, REST, API设计',
            ]
        );
    }

    private function createComments(): void
    {
        $posts = Post::published()->get();

        $comments = [
            ['nickname' => '张三', 'email' => 'zhangsan@example.com', 'content' => '写得很详细，受益匪浅！特别是关于索引设计的部分，解决了我项目中的慢查询问题。'],
            ['nickname' => '李四', 'email' => 'lisi@example.com', 'content' => '请问多阶段构建对构建时间的影响大吗？我这边 CI 时间本来就比较长。'],
            ['nickname' => '王五', 'email' => 'wangwu@example.com', 'content' => 'Tailwind v4 的升级确实有点折腾，不过编译速度提升很明显，值了。'],
            ['nickname' => 'Alice', 'email' => 'alice@example.com', 'content' => 'Great article! Would love to see more about Laravel optimization tips.'],
            ['nickname' => 'Bob', 'email' => 'bob@example.com', 'content' => 'Could you share the Dockerfile template you used? That would be very helpful.'],
            ['nickname' => '陈六', 'email' => 'chenliu@example.com', 'content' => '博主更新频率很高啊，持续关注中。'],
            ['nickname' => '赵七', 'email' => 'zhaoqi@example.com', 'content' => '关于缓存穿透和雪崩的处理可以展开讲讲吗？'],
            ['nickname' => 'Sarah', 'email' => 'sarah@example.com', 'content' => 'The comparison between Git Flow and Trunk-based development is spot on. Thanks for sharing!'],
            ['nickname' => '周八', 'email' => 'zhouba@example.com', 'content' => 'Serverless 的冷启动问题在实际项目中影响大吗？'],
            ['nickname' => '吴九', 'email' => 'wujiu@example.com', 'content' => 'Nginx 调优部分写得太好了，按照步骤操作后 QPS 提升了 40%。'],
        ];

        foreach ($comments as $data) {
            Comment::create([
                'post_id' => $posts->random()->id,
                'parent_id' => null,
                'nickname' => $data['nickname'],
                'email' => $data['email'],
                'website' => fake()->optional()->url(),
                'content' => $data['content'],
                'is_approved' => true,
                'ip' => fake()->ipv4(),
            ]);
        }

        // Create some nested replies
        $approvedComments = Comment::where('is_approved', true)->get();
        if ($approvedComments->count() >= 3) {
            Comment::create([
                'post_id' => $approvedComments->first()->post_id,
                'parent_id' => $approvedComments->first()->id,
                'nickname' => 'SnowmanNunu',
                'email' => 'admin@example.com',
                'content' => '感谢反馈！如果有具体问题欢迎邮件交流。',
                'is_approved' => true,
                'ip' => fake()->ipv4(),
            ]);

            Comment::create([
                'post_id' => $approvedComments->skip(1)->first()->post_id,
                'parent_id' => $approvedComments->skip(1)->first()->id,
                'nickname' => 'SnowmanNunu',
                'email' => 'admin@example.com',
                'content' => '这个问题问得好，我计划下一篇文章专门展开讲。',
                'is_approved' => true,
                'ip' => fake()->ipv4(),
            ]);
        }
    }

    private function createGuestbooks(): void
    {
        $entries = [
            ['nickname' => '访客A', 'email' => 'visitor-a@example.com', 'content' => '博客界面很简洁，加载速度也很快，体验不错！'],
            ['nickname' => '访客B', 'email' => 'visitor-b@example.com', 'content' => '技术文章质量很高，收藏了。希望博主继续保持更新。'],
            ['nickname' => '老读者', 'email' => 'oldreader@example.com', 'content' => '从第一篇文章就开始关注了，见证了博客的成长，加油！'],
            ['nickname' => '新手程序员', 'email' => 'newbie@example.com', 'content' => '作为 Laravel 新手，这里的文章对我帮助很大，特别是 Best Practices 分类。'],
            ['nickname' => '开源爱好者', 'email' => 'oss@example.com', 'content' => '项目开源的做法很赞，已经 Star 了，希望能贡献代码。'],
        ];

        foreach ($entries as $data) {
            Guestbook::create([
                'nickname' => $data['nickname'],
                'email' => $data['email'],
                'content' => $data['content'],
                'is_approved' => true,
                'ip' => fake()->ipv4(),
            ]);
        }

        // Create one with reply
        Guestbook::create([
            'nickname' => '提问者',
            'email' => 'question@example.com',
            'content' => '博主能不能分享一下你的开发环境配置？',
            'reply' => '当然可以，我的主力环境是 macOS + Laravel Herd + VS Code + TablePlus，具体插件配置我找机会写一篇文章。',
            'replied_at' => now()->subDays(2),
            'is_approved' => true,
            'ip' => fake()->ipv4(),
        ]);
    }

    private function createLinks(): void
    {
        $links = [
            ['name' => 'Laravel 中文网', 'url' => 'https://learnku.com/laravel', 'description' => 'Laravel 中文社区', 'is_visible' => true, 'sort_order' => 1],
            ['name' => 'Tailwind CSS', 'url' => 'https://tailwindcss.com', 'description' => '实用优先的 CSS 框架', 'is_visible' => true, 'sort_order' => 2],
            ['name' => 'Filament', 'url' => 'https://filamentphp.com', 'description' => 'Laravel TALL 栈后台面板', 'is_visible' => true, 'sort_order' => 3],
            ['name' => 'GitHub', 'url' => 'https://github.com', 'description' => '全球最大的代码托管平台', 'is_visible' => true, 'sort_order' => 4],
            ['name' => 'Packagist', 'url' => 'https://packagist.org', 'description' => 'PHP 包仓库', 'is_visible' => true, 'sort_order' => 5],
        ];

        foreach ($links as $link) {
            Link::firstOrCreate(['url' => $link['url']], $link);
        }
    }

    private function articleContent(string $topic): string
    {
        $paragraphs = [
            "## 引言\n\n在当下的技术生态中，{$topic} 已经成为开发者不可忽视的重要话题。无论是新项目的技术选型，还是现有系统的优化升级，深入理解 {$topic} 的核心原理和最佳实践都至关重要。",
            "## 核心概念\n\n{$topic} 的设计哲学强调简洁与高效的平衡。从架构层面来看，它通过模块化的组件设计，让开发者可以根据实际需求灵活组合功能，而不必引入不必要的复杂度。",
            "## 实践方案\n\n在实际项目中应用 {$topic}，建议遵循以下步骤：\n\n1. **环境准备**：确保开发环境满足最低版本要求\n2. **基础配置**：根据官方文档完成核心参数的配置\n3. **功能验证**：通过单元测试和集成测试验证关键路径\n4. **性能调优**：基于实际负载数据进行针对性优化\n5. **监控部署**：建立完善的日志和监控体系",
            "## 常见问题\n\n在实践过程中，开发者常会遇到一些典型问题：\n\n- 配置冲突导致服务无法正常启动\n- 高并发场景下的性能瓶颈\n- 版本升级时的兼容性处理\n\n针对这些问题，建议提前建立完整的测试覆盖，并在预发布环境中充分验证。",
            "## 总结\n\n{$topic} 为现代 Web 开发提供了强大而优雅的解决方案。通过合理的架构设计和持续的性能优化，我们可以在保证开发效率的同时，构建出稳定、可扩展的应用系统。希望本文的分享能为你的项目实践带来启发。",
        ];

        return implode("\n\n", $paragraphs);
    }
}
