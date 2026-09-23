<?php

namespace App\Filament\Pages;

use App\Models\AboutUs;
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
 * Settings page for the About Us page's Company Video (title/description/
 * YouTube link) — split out from the Visi Misi page so it has its own
 * menu item, same as Banner. There is always exactly one AboutUs row, so
 * this uses a plain form bound to it instead of a list/create/delete
 * Resource, same as ManageAboutUs/ManageBanner.
 */
class ManageCompanyVideo extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?string $navigationLabel = 'Company Video';

    protected static ?string $title = 'Company Video';

    protected static string|UnitEnum|null $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 6;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            AboutUs::singleton()->only([
                'video_title_id',
                'video_title_en',
                'video_description_id',
                'video_description_en',
                'video_youtube_url',
            ]),
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Company Video')
                    ->schema([
                        TextInput::make('video_title_id')->label('Title (Indonesian)')->maxLength(255),
                        TextInput::make('video_title_en')->label('Title (English)')->maxLength(255),
                        Textarea::make('video_description_id')->label('Description (Indonesian)')->rows(3),
                        Textarea::make('video_description_en')->label('Description (English)')->rows(3),
                        TextInput::make('video_youtube_url')
                            ->label('YouTube video link')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        AboutUs::singleton()->update($this->form->getState());

        Notification::make()
            ->title('Company Video saved')
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
