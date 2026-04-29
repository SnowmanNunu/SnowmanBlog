<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularPostsWidget extends BaseWidget
{
    protected static ?string $heading = '热门文章 TOP10';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->withCount('views')
                    ->orderBy('views_count', 'desc')
                    ->limit(10)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->limit(40)
                    ->url(fn ($record) => '/blog/' . $record->slug)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分类')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('浏览量')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('点赞数')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->dateTime('Y-m-d'),
            ]);
    }
}
