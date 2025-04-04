<?php

namespace App\Filament\Resources\BackpackResource\Pages;

use App\Filament\Resources\BackpackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBackpacks extends ListRecords
{
    protected static string $resource = BackpackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
