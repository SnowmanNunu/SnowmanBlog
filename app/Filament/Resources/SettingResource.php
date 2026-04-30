<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = '网站配置';

    protected static ?string $modelLabel = '配置项';

    protected static ?string $pluralModelLabel = '配置项';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->label('配置键名')
                    ->disabledOn('edit'),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('配置名称'),
                Forms\Components\Textarea::make('value')
                    ->maxLength(65535)
                    ->label('配置值')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->label('配置名称'),
                Tables\Columns\TextColumn::make('value')
                    ->wrap()
                    ->label('配置值')
                    ->limit(50),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->label('键名'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('编辑'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
