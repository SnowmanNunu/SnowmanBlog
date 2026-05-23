<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = '评论管理';

    protected static ?string $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '评论';

    protected static ?string $pluralModelLabel = '评论';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('post_id')->relationship('post', 'title')->required()->label('文章'),
                Forms\Components\TextInput::make('nickname')->required()->label('昵称'),
                Forms\Components\TextInput::make('email')->email()->label('邮箱'),
                Forms\Components\TextInput::make('website')->url()->label('网站'),
                Forms\Components\Textarea::make('content')->required()->label('内容'),
                Forms\Components\Toggle::make('is_approved')->label('审核通过'),
                Forms\Components\Toggle::make('is_admin')->label('博主回复'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')->limit(30)->label('文章'),
                Tables\Columns\TextColumn::make('nickname')->searchable()->label('昵称'),
                Tables\Columns\TextColumn::make('content')->limit(50)->label('内容'),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('已审核')->sortable(),
                Tables\Columns\IconColumn::make('is_admin')->boolean()->label('博主')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('时间')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('审核状态'),
                Tables\Filters\TernaryFilter::make('is_admin')->label('博主回复'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('通过')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (Comment $record): bool => ! $record->is_approved)
                    ->action(function (Comment $record) {
                        $record->update(['is_approved' => true]);
                        Notification::make()
                            ->success()
                            ->title('已通过')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('审核通过')
                    ->modalDescription(fn (Comment $record): string => "确定通过 {$record->nickname} 的评论吗？")
                    ->modalSubmitActionLabel('确认通过'),
                Tables\Actions\Action::make('reject')
                    ->label('拒绝')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (Comment $record): bool => $record->is_approved)
                    ->action(function (Comment $record) {
                        $record->update(['is_approved' => false]);
                        Notification::make()
                            ->success()
                            ->title('已拒绝')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('拒绝评论')
                    ->modalDescription(fn (Comment $record): string => "确定拒绝 {$record->nickname} 的评论吗？")
                    ->modalSubmitActionLabel('确认拒绝'),
                Tables\Actions\Action::make('reply')
                    ->label('回复')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('info')
                    ->form([
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->label('回复内容')
                            ->placeholder('请输入回复内容…'),
                    ])
                    ->action(function (array $data, Comment $record) {
                        Comment::create([
                            'post_id' => $record->post_id,
                            'parent_id' => $record->id,
                            'nickname' => auth()->user()->name ?? '博主',
                            'email' => auth()->user()->email ?? '',
                            'content' => $data['content'],
                            'is_admin' => true,
                            'is_approved' => true,
                        ]);
                        Notification::make()
                            ->success()
                            ->title('回复成功')
                            ->send();
                    })
                    ->modalHeading('回复评论')
                    ->modalSubmitActionLabel('提交回复'),
                Tables\Actions\EditAction::make()->label('编辑'),
                Tables\Actions\DeleteAction::make()->label('删除'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveBulk')
                        ->label('批量通过')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_approved' => true]);
                            Notification::make()
                                ->success()
                                ->title("已批量通过 {$records->count()} 条评论")
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()->label('删除'),
                ])->label('批量操作'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
