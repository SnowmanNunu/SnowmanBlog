<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Guestbook;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $todayCount = Post::whereDate('created_at', today())->count();
        $yesterdayCount = Post::whereDate('created_at', today()->subDay())->count();
        $trend = $yesterdayCount > 0 ? round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100) . '%' : ($todayCount > 0 ? '+100%' : '0%');

        return [
            Stat::make('文章总数', Post::count())
                ->description('全部文章')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('今日发布', $todayCount)
                ->description('较昨日 ' . $trend)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('留言总数', Guestbook::count())
                ->description('待回复 ' . Guestbook::whereNull('reply')->count())
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),
            Stat::make('评论总数', Comment::count())
                ->description('全部评论')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('info'),
        ];
    }
}