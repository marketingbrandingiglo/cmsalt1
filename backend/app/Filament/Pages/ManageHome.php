<?php

namespace App\Filament\Pages;

use App\Models\HomePage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Single settings page for the Home page section copy (Intro, Products
 * section heading, News section heading) — there is always exactly one
 * HomePage row. Hero slides are managed in HeroSlideResource.
 */
class ManageHome extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Home Sections';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Home';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(HomePage::singleton()->only(HomePage::FIELDS));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Intro / Tagline')
                    ->description('Section right below the hero slider.')
                    ->schema([
                        TextInput::make('intro_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
                        TextInput::make('intro_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
                        Textarea::make('intro_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                        Textarea::make('intro_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                        Textarea::make('intro_description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
                        Textarea::make('intro_description_en')->label('Description (English)')->maxLength(1000)->rows(3),
                        TextInput::make('intro_cta_label_id')->label('Button label (Indonesian)')->maxLength(100),
                        TextInput::make('intro_cta_label_en')->label('Button label (English)')->maxLength(100),
                        TextInput::make('intro_cta_url')
                            ->label('Button link')
                            ->placeholder('/about')
                            ->helperText('Site path starting with "/" or a full http(s) URL.')
                            ->regex(HomePage::LINK_REGEX)
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Products & Solutions section')
                    ->description('Heading above the product carousel.')
                    ->schema([
                        TextInput::make('products_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
                        TextInput::make('products_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
                        Textarea::make('products_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                        Textarea::make('products_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                        Textarea::make('products_description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
                        Textarea::make('products_description_en')->label('Description (English)')->maxLength(1000)->rows(3),
                    ])
                    ->columns(2),
                Section::make('News section')
                    ->description('Heading above the latest news grid.')
                    ->schema([
                        TextInput::make('news_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
                        TextInput::make('news_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
                        Textarea::make('news_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                        Textarea::make('news_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        HomePage::singleton()->update($this->form->getState());

        Notification::make()
            ->title('Home saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->key('form-actions'),
            ]);
    }
}
