<?php

namespace App\Filament\Pages\Home;

use App\Models\HomePage;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/** Home page Intro / Tagline section, right below the hero slider. */
class ManageHomeIntro extends HomeSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $navigationLabel = 'Intro';

    protected static ?string $title = 'Intro';

    protected static ?int $navigationSort = 1;

    protected function fields(): array
    {
        return [
            TextInput::make('intro_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
            TextInput::make('intro_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
            Textarea::make('intro_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
            Textarea::make('intro_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
            Textarea::make('intro_description_en')->label('Description (English)')->maxLength(1000)->rows(3),
            Textarea::make('intro_description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
            TextInput::make('intro_cta_label_en')->label('Button label (English)')->maxLength(100),
            TextInput::make('intro_cta_label_id')->label('Button label (Indonesian)')->maxLength(100),
            TextInput::make('intro_cta_url')
                ->label('Button link')
                ->placeholder('/about')
                ->helperText('Site path starting with "/" or a full http(s) URL.')
                ->regex(HomePage::LINK_REGEX)
                ->maxLength(255),
        ];
    }
}
