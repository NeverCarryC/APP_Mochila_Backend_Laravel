<?php

namespace App\Filament\Resources\TemplateBackpackResource\Pages;

use App\Filament\Resources\TemplateBackpackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateBackpack extends EditRecord
{
    protected static string $resource = TemplateBackpackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
