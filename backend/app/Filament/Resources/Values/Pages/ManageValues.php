<?php

namespace App\Filament\Resources\Values\Pages;

use App\Filament\Resources\Values\ValueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageValues extends ManageRecords
{
    protected static string $resource = ValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
