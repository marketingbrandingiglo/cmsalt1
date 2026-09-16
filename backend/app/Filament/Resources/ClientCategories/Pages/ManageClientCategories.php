<?php

namespace App\Filament\Resources\ClientCategories\Pages;

use App\Filament\Resources\ClientCategories\ClientCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClientCategories extends ManageRecords
{
    protected static string $resource = ClientCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
