<?php

namespace App\Filament\Pages\Home;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/** Heading above the Home page's Products & Solutions carousel. */
class ManageHomeProducts extends HomeSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Produk & Solusi';

    protected static ?string $title = 'Produk & Solusi';

    protected static ?int $navigationSort = 2;

    protected function fields(): array
    {
        return [
            TextInput::make('products_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
            TextInput::make('products_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
            Textarea::make('products_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
            Textarea::make('products_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
            Textarea::make('products_description_en')->label('Description (English)')->maxLength(1000)->rows(3),
            Textarea::make('products_description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
        ];
    }
}
