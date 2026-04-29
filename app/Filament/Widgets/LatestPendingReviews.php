<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Guestbook;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class LatestPendingReviews extends BaseWidget
{
    protected static ?string $heading = '最新待审内容';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DB::table('comments')
                    ->select('id', 'nickname', 'content', 'post_id', 'created_at', DB::raw("'评论' as type"))
                    ->where('is_approved', false)
                    ->union(
                        DB::table('guestbooks')
                            ->select('id', 'nickname', 'content', DB::raw('NULL as post_id'), 'created_at', DB::raw("'留言' as type"))
                            ->where('is_approved', false)
                    )
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '评论' => 'primary',
                        '留言' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nickname')
                    ->label('昵称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('内容')
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record->content),
                Tables\Columns\TextColumn::make('post_title')
                    ->label('关联文章')
                    ->placeholder('留言板')
                    ->getStateUsing(function ($record) {
                        if ($record->post_id) {
                            return DB::table('posts')->where('id', $record->post_id)->value('title');
                        }
                        return null;
                    })
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('提交时间')
                    ->dateTime('m-d H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('通过')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record->type === '评论') {
                            Comment::where('id', $record->id)->update(['is_approved' => true]);
                        } else {
                            Guestbook::where('id', $record->id)->update(['is_approved' => true]);
                        }
                    }),
                Tables\Actions\Action::make('delete')
                    ->label('删除')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record->type === '评论') {
                            Comment::where('id', $record->id)->delete();
                        } else {
                            Guestbook::where('id', $record->id)->delete();
                        }
                    }),
            ]);
    }
}
