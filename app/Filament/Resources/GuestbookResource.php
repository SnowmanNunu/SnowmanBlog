<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestbookResource\Pages;
use App\Models\Guestbook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GuestbookResource extends Resource
{
    protected static ?string $model = Guestbook::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = '留言管理';

    protected static ?string $modelLabel = '留言';

    protected static ?string $pluralModelLabel = '留言';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nickname')->required()->label('昵称'),
                Forms\Components\TextInput::make('email')->email()->label('邮箱'),
                Forms\Components\TextInput::make('website')->url()->label('网站'),
                Forms\Components\Textarea::make('content')->required()->label('内容'),
                Forms\Components\Textarea::make('reply')->label('博主回复'),
                Forms\Components\Toggle::make('is_approved')->label('审核通过'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nickname')->searchable()->label('昵称'),
                Tables\Columns\TextColumn::make('content')->limit(50)->label('内容'),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('已审核')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('提交时间')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('审核状态'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleApprove')
                    ->label(fn (Guestbook $record): string => $record->is_approved ? '取消审核' : '通过')
                    ->icon(fn (Guestbook $record): string => $record->is_approved ? 'heroicon-m-x-mark' : 'heroicon-m-check')
                    ->color(fn (Guestbook $record): string => $record->is_approved ? 'warning' : 'success')
                    ->action(function (Guestbook $record) {
                        $record->update(['is_approved' => ! $record->is_approved]);
                        Notification::make()
                            ->success()
                            ->title($record->is_approved ? '已通过' : '已取消审核')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (Guestbook $record): string => $record->is_approved ? '取消审核' : '审核通过')
                    ->modalDescription(fn (Guestbook $record): string => $record->is_approved
                        ? "确定取消审核 {$record->nickname} 的留言吗？"
                        : "确定通过 {$record->nickname} 的留言吗？")
                    ->modalSubmitActionLabel(fn (Guestbook $record): string => $record->is_approved ? '取消审核' : '确认通过'),
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
                                ->title("已批量通过 {$records->count()} 条留言")
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()->label('删除'),
                ])->label('批量操作'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuestbooks::route('/'),
            'edit' => Pages\EditGuestbook::route('/{record}/edit'),
        ];
    }
}
