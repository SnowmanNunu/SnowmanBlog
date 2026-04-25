<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    public function getTitle(): string
    {
        return '编辑文章';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('删除'),
        ];
    }
}