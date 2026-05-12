<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = '邮件订阅';

    protected static ?string $modelLabel = '订阅者';

    protected static ?string $pluralModelLabel = '订阅者';

    protected static ?string $navigationGroup = '内容管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true),
                Forms\Components\DateTimePicker::make('verified_at')
                    ->label('验证时间')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('已验证')
                    ->boolean()
                    ->getStateUsing(fn (Subscriber $record) => $record->isVerified()),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('订阅时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label('已验证')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->label('未验证')
                    ->query(fn (Builder $query): Builder => $query->whereNull('verified_at')),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
        ];
    }
}
