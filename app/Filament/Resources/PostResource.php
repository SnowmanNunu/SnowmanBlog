<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = '文章管理';

    protected static ?string $modelLabel = '文章';

    protected static ?string $pluralModelLabel = '文章';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\View::make('components.draft-autosave')
                    ->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('分类'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('作者'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('标题'),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->label('别名'),
                Forms\Components\Section::make('SEO 设置')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta 标题')
                            ->maxLength(255)
                            ->placeholder('留空则使用文章标题'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta 描述')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('留空则使用文章摘要'),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta 关键词')
                            ->maxLength(255)
                            ->placeholder('关键词用英文逗号分隔'),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('excerpt')
                    ->label('摘要')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->label('内容')
                    ->columnSpanFull()
                    ->fileAttachmentsDisk(config('filesystems.media_disk', 'public'))
                    ->fileAttachmentsDirectory('attachments')
                    ->hint('图片建议不超过 10MB'),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('封面图')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->image()
                    ->maxSize(10240)
                    ->imageResizeTargetWidth(1200)
                    ->columnSpanFull(),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('置顶')
                            ->default(false),
                        Forms\Components\Toggle::make('is_published')
                            ->label('已发布')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('发布时间')
                            ->hint('选择未来时间可实现定时发布，到达时间后系统每分钟自动发布')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分类')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('作者')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('别名')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('封面图'),
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('置顶')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('已发布')
                    ->boolean(),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('点赞数')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('编辑'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('删除'),
                ])->label('批量操作'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
