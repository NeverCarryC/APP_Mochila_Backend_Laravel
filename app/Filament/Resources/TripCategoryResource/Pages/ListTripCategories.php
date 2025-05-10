<?php

namespace App\Filament\Resources\TripCategoryResource\Pages;

use App\Filament\Resources\TripCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTripCategories extends ListRecords
{
    protected static string $resource = TripCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
