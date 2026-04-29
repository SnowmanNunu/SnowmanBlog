<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Guestbook;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $todayCount = Post::whereDate('created_at', today())->count();
        $yesterdayCount = Post::whereDate('created_at', today()->subDay())->count();
        $trend = $yesterdayCount > 0 ? round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100) . '%' : ($todayCount > 0 ? '+100%' : '0%');
        $pendingGuestbooks = Guestbook::where('is_approved', false)->count();
        $pendingComments = Comment::where('is_approved', false)->count();

        // 今日访问统计（缓存5分钟）
        $todayStats = Cache::remember('dashboard:today_stats', 300, function () {
            $pv = DB::table('post_views')->whereDate('viewed_at', today())->count();
            $uv = DB::table('post_views')->whereDate('viewed_at', today())->distinct('ip_address')->count('ip_address');
            $yesterdayPv = DB::table('post_views')->whereDate('viewed_at', today()->subDay())->count();
            return [
                'pv' => $pv,
                'uv' => $uv,
                'yesterday_pv' => $yesterdayPv,
            ];
        });

        $pvTrend = $todayStats['yesterday_pv'] > 0
            ? round((($todayStats['pv'] - $todayStats['yesterday_pv']) / $todayStats['yesterday_pv']) * 100) . '%'
            : ($todayStats['pv'] > 0 ? '+100%' : '0%');

        $stats = [
            Stat::make('文章总数', Post::count())
                ->description('全部文章')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('今日访问', $todayStats['pv'])
                ->description("UV {$todayStats['uv']} | 较昨日 {$pvTrend}")
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
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

        if ($pendingGuestbooks > 0) {
            $stats[] = Stat::make('待审核留言', $pendingGuestbooks)
                ->url('/admin/guestbooks')
                ->description('点击去审核')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger');
        }

        if ($pendingComments > 0) {
            $stats[] = Stat::make('待审核评论', $pendingComments)
                ->url('/admin/comments')
                ->description('点击去审核')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger');
        }

        return $stats;
    }
}
