<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    public function getHeading(): string
    {
        return '文章列表';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('新建文章'),
        ];
    }
}