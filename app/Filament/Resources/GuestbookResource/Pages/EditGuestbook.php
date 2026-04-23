<?php
namespace App\Filament\Resources\GuestbookResource\Pages;
use App\Filament\Resources\GuestbookResource;
use Filament\Resources\Pages\EditRecord;
class EditGuestbook extends EditRecord
{
    protected static string $resource = GuestbookResource::class;
}