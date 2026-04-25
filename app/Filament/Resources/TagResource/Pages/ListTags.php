<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    public function getHeading(): string
    {
        return '标签列表';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('新建标签'),
        ];
    }
}