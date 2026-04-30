<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    public function getTitle(): string
    {
        return '编辑标签';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('删除'),
        ];
    }
}
