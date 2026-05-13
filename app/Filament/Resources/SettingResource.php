<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = '网站配置';

    protected static ?string $navigationGroup = '系统设置';

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
                Forms\Components\Select::make('type')
                    ->required()
                    ->label('类型')
                    ->options([
                        'text' => '文本',
                        'textarea' => '多行文本',
                        'image' => '图片',
                    ])
                    ->default('text')
                    ->live(),
                Forms\Components\Placeholder::make('current_image')
                    ->label('当前图片')
                    ->content(function ($record): HtmlString {
                        if (! $record || ! $record->value) {
                            return new HtmlString('<span class="text-gray-400 text-sm">暂无图片</span>');
                        }

                        return new HtmlString(
                            '<img src="' . asset($record->value) . '" class="h-16 w-auto rounded shadow-sm border border-gray-200">'
                        );
                    })
                    ->hidden(fn (Forms\Get $get) => $get('type') !== 'image'),
                Forms\Components\Textarea::make('value')
                    ->maxLength(65535)
                    ->label('配置值')
                    ->columnSpanFull()
                    ->hidden(fn (Forms\Get $get) => $get('type') === 'image')
                    ->dehydrated(fn (Forms\Get $get) => $get('type') !== 'image'),
                Forms\Components\FileUpload::make('value')
                    ->label('上传新图片')
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->visibility('public')
                    ->columnSpanFull()
                    ->hidden(fn (Forms\Get $get) => $get('type') !== 'image')
                    ->dehydrated(fn (Forms\Get $get) => $get('type') === 'image'),
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
                Tables\Columns\TextColumn::make('type')
                    ->label('类型'),
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
