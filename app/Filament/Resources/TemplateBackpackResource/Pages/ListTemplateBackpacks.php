<?php

namespace App\Filament\Resources\TemplateBackpackResource\Pages;

use App\Filament\Resources\TemplateBackpackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateBackpacks extends ListRecords
{
    protected static string $resource = TemplateBackpackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
