<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = '操作日志';
    protected static ?string $modelLabel = '操作日志';
    protected static ?string $pluralModelLabel = '操作日志';
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->label('日志分组'),
                Forms\Components\TextInput::make('event')
                    ->label('事件'),
                Forms\Components\TextInput::make('description')
                    ->label('描述'),
                Forms\Components\KeyValue::make('properties.attributes')
                    ->label('新值'),
                Forms\Components\KeyValue::make('properties.old')
                    ->label('旧值'),
                Forms\Components\TextInput::make('causer.name')
                    ->label('操作人'),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('时间'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('分组')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'post' => 'primary',
                        'comment' => 'info',
                        'guestbook' => 'warning',
                        'setting' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'post' => '文章',
                        'comment' => '评论',
                        'guestbook' => '留言',
                        'setting' => '设置',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('事件')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'created' => '创建',
                        'updated' => '更新',
                        'deleted' => '删除',
                        default => $state ?? '-',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column, $record): ?string {
                        return $record->description;
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('对象')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        $short = class_basename($state);
                        return $short . ($record->subject_id ? ' #' . $record->subject_id : '');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('操作人')
                    ->placeholder('系统')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('时间')
                    ->dateTime('m-d H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('分组')
                    ->options([
                        'post' => '文章',
                        'comment' => '评论',
                        'guestbook' => '留言',
                        'setting' => '设置',
                    ]),
                Tables\Filters\SelectFilter::make('event')
                    ->label('事件')
                    ->options([
                        'created' => '创建',
                        'updated' => '更新',
                        'deleted' => '删除',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('查看'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('删除'),
                ])->label('批量操作'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
