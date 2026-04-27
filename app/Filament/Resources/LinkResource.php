<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = '友情链接';
    protected static ?string $modelLabel = '友情链接';
    protected static ?string $pluralModelLabel = '友情链接';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('站点名称')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('url')
                ->label('链接地址')
                ->required()
                ->url()
                ->maxLength(255),
            Forms\Components\TextInput::make('description')
                ->label('站点描述')
                ->maxLength(255),
            Forms\Components\Toggle::make('is_visible')
                ->label('是否显示')
                ->default(true),
            Forms\Components\TextInput::make('sort_order')
                ->label('排序')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('站点名称')
                ->searchable(),
            Tables\Columns\TextColumn::make('url')
                ->label('链接地址')
                ->searchable(),
            Tables\Columns\IconColumn::make('is_visible')
                ->label('显示状态')
                ->boolean(),
            Tables\Columns\TextColumn::make('sort_order')
                ->label('排序')
                ->numeric()
                ->sortable(),
        ])
        ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ManageLinks::route('/'),
        ];
    }
}