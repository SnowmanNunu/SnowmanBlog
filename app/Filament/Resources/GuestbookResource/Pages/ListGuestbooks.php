<?php

namespace App\Filament\Resources\GuestbookResource\Pages;

use App\Filament\Resources\GuestbookResource;
use Filament\Resources\Pages\ListRecords;

class ListGuestbooks extends ListRecords
{
    protected static string $resource = GuestbookResource::class;

    public function getHeading(): string
    {
        return '留言列表';
    }
}
