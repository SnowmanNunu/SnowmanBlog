<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = '评论管理';
    protected static ?string $modelLabel = '评论';
    protected static ?string $pluralModelLabel = '评论';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('post_id')->relationship('post', 'title')->required()->label('文章'),
                Forms\Components\TextInput::make('nickname')->required()->label('昵称'),
                Forms\Components\TextInput::make('email')->email()->required()->label('邮箱'),
                Forms\Components\Textarea::make('content')->required()->label('内容'),
                Forms\Components\Toggle::make('is_approved')->label('审核通过'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')->limit(30)->label('文章'),
                Tables\Columns\TextColumn::make('nickname')->searchable()->label('昵称'),
                Tables\Columns\TextColumn::make('content')->limit(50)->label('内容'),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('已审核'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('时间'),
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
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}