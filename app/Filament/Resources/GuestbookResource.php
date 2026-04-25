<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestbookResource\Pages;
use App\Models\Guestbook;
use Filament\Forms;
use Filament\Forms\Form;
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
                FormsComponentsTextInput::make('nickname')->required()->label('昵称'),
                FormsComponentsTextInput::make('email')->email()->label('邮箱'),
                FormsComponentsTextInput::make('website')->url()->label('网站'),
                FormsComponentsTextarea::make('content')->required()->label('内容'),
                FormsComponentsTextarea::make('reply')->label('博主回复'),
                FormsComponentsToggle::make('is_approved')->label('审核通过'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TablesColumnsTextColumn::make('nickname')->searchable()->label('昵称'),
                TablesColumnsTextColumn::make('content')->limit(50)->label('内容'),
                TablesColumnsIconColumn::make('is_approved')->boolean()->label('已审核'),
                TablesColumnsTextColumn::make('created_at')->dateTime()->label('提交时间'),
            ])
            ->filters([
                TablesFiltersTernaryFilter::make('is_approved')->label('审核状态'),
            ])
            ->actions([
                TablesActionsEditAction::make()->label('编辑'),
                TablesActionsDeleteAction::make()->label('删除'),
            ])
            ->bulkActions([
                TablesActionsBulkActionGroup::make([
                    TablesActionsDeleteBulkAction::make()->label('删除'),
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