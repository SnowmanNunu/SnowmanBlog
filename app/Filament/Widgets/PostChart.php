<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;

class PostChart extends ChartWidget
{
    protected static ?string $heading = '最近30天文章发布趋势';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate = now()->subDays(29)->format('Y-m-d');

        $raw = Post::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereDate('created_at', '>=', $startDate)
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'date');

        $dates = [];
        $counts = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('m-d');
            $counts[] = $raw[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => '文章数',
                    'data' => $counts,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
