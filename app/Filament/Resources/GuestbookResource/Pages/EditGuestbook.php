<?php

namespace App\Filament\Resources\GuestbookResource\Pages;

use App\Filament\Resources\GuestbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuestbook extends EditRecord
{
    protected static string $resource = GuestbookResource::class;

    public function getTitle(): string
    {
        return '编辑留言';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('删除'),
        ];
    }
}
