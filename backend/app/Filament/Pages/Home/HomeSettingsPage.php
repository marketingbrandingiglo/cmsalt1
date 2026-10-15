<?php

namespace App\Filament\Pages\Home;

use App\Models\HomePage;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * Base for the Home page's single-form menu items (Intro, Produk & Solusi,
 * Berita) under the "Home" sidebar group. Each edits a slice of the one
 * HomePage row, same pattern as the About Us settings pages.
 */
abstract class HomeSettingsPage extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|UnitEnum|null $navigationGroup = 'Home';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @return list<Component|Field> */
    abstract protected function fields(): array;

    public function mount(): void
    {
        $this->form->fill(HomePage::singleton()->only(HomePage::FIELDS));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make(static::$title)
                    ->schema($this->fields())
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        HomePage::singleton()->update($this->form->getState());

        Notification::make()
            ->title(static::$title.' saved')
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
