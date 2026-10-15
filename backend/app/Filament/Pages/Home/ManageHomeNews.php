<?php

namespace App\Filament\Pages\Home;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/** Heading above the Home page's latest-news grid. */
class ManageHomeNews extends HomeSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'Berita';

    protected static ?string $title = 'Berita';

    protected static ?int $navigationSort = 3;

    protected function fields(): array
    {
        return [
            TextInput::make('news_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
            TextInput::make('news_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
            Textarea::make('news_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
            Textarea::make('news_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
        ];
    }
}
