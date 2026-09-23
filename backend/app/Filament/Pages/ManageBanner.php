<?php

namespace App\Filament\Pages;

use App\Models\AboutUs;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
 * Settings page for the About Us page banner (image/title/description) —
 * split out from the Visi Misi page so it has its own menu item. There is
 * always exactly one AboutUs row, so this uses a plain form bound to it
 * instead of a list/create/delete Resource, same as ManageAboutUs.
 */
class ManageBanner extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Banner';

    protected static ?string $title = 'Banner';

    protected static string|UnitEnum|null $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 0;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            AboutUs::singleton()->only([
                'banner_image_path',
                'banner_title_id',
                'banner_title_en',
                'banner_description_id',
                'banner_description_en',
            ]),
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Banner')
                    ->schema([
                        FileUpload::make('banner_image_path')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('about-banner')
                            ->columnSpanFull(),
                        TextInput::make('banner_title_en')->label('Title (English)')->maxLength(255),
                        TextInput::make('banner_title_id')->label('Title (Indonesian)')->maxLength(255),
                        Textarea::make('banner_description_en')->label('Description (English)')->rows(3),
                        Textarea::make('banner_description_id')->label('Description (Indonesian)')->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        AboutUs::singleton()->update($this->form->getState());

        Notification::make()
            ->title('Banner saved')
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
