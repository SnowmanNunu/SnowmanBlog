<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = '缓存管理';

    protected static ?string $title = '缓存管理';

    protected static ?string $slug = 'cache-manager';

    protected static string $view = 'filament.pages.cache-manager';

    public array $selectedCaches = [];

    public function getCacheGroups(): array
    {
        return [
            'posts' => [
                'label' => '文章缓存',
                'description' => '包含首页文章列表、分类文章、标签文章、热门文章等',
                'icon' => 'heroicon-m-document-text',
                'color' => 'primary',
            ],
            'settings' => [
                'label' => '设置缓存',
                'description' => '站点标题、描述、ICP 备案等基础配置缓存',
                'icon' => 'heroicon-m-cog-6-tooth',
                'color' => 'warning',
            ],
            'views' => [
                'label' => '浏览量缓存',
                'description' => '文章浏览统计相关缓存',
                'icon' => 'heroicon-m-eye',
                'color' => 'info',
            ],
            'search' => [
                'label' => '搜索缓存',
                'description' => '热门搜索关键词、搜索结果缓存',
                'icon' => 'heroicon-m-magnifying-glass',
                'color' => 'success',
            ],
        ];
    }

    public function clearSelectedCaches(): void
    {
        if (empty($this->selectedCaches)) {
            Notification::make()
                ->warning()
                ->title('请先选择要清除的缓存类型')
                ->send();

            return;
        }

        foreach ($this->selectedCaches as $tag) {
            try {
                Cache::tags([$tag])->flush();
            } catch (\Exception $e) {
                // 如果驱动不支持 tags，则跳过
            }
        }

        Notification::make()
            ->success()
            ->title('已清除选中的缓存')
            ->body('共清除 '.count($this->selectedCaches).' 个缓存分组')
            ->send();

        $this->selectedCaches = [];
    }

    public function clearAllCaches(): void
    {
        try {
            foreach (array_keys($this->getCacheGroups()) as $tag) {
                Cache::tags([$tag])->flush();
            }
        } catch (\Exception $e) {
            // 部分驱动可能不支持 tags
        }

        Artisan::call('cache:clear');

        Notification::make()
            ->success()
            ->title('已全部清除')
            ->body('应用缓存与分组缓存已清空')
            ->send();

        $this->selectedCaches = [];
    }

    public function clearRouteCache(): void
    {
        Artisan::call('route:clear');
        Notification::make()
            ->success()
            ->title('路由缓存已清除')
            ->send();
    }

    public function clearViewCache(): void
    {
        Artisan::call('view:clear');
        Notification::make()
            ->success()
            ->title('视图缓存已清除')
            ->send();
    }

    public function clearConfigCache(): void
    {
        Artisan::call('config:clear');
        Notification::make()
            ->success()
            ->title('配置缓存已清除')
            ->send();
    }

    public function optimizeApp(): void
    {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        Notification::make()
            ->success()
            ->title('应用已优化')
            ->body('配置、路由、视图已重新缓存')
            ->send();
    }
}
