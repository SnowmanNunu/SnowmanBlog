<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use Filament\Widgets\ChartWidget;

class BlogViewsChart extends ChartWidget
{
    protected static ?string $heading = '最近30天浏览量趋势';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate = now()->subDays(29)->format('Y-m-d');

        $raw = DB::table('post_views')
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
            ->whereDate('viewed_at', '>=', $startDate)
            ->groupByRaw('DATE(viewed_at)')
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
                    'label' => '浏览量',
                    'data' => $counts,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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
