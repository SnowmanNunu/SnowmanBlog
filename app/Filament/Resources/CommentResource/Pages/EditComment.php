<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    public function getTitle(): string
    {
        return '编辑评论';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('删除'),
        ];
    }
}
