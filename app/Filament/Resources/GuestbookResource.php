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
                Forms\Components\TextInput::make('nickname')->required()->label('昵称'),
                Forms\Components\TextInput::make('email')->email()->required()->label('邮箱'),
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
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('已审核'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('提交时间'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('审核状态'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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