<?php

namespace App\Filament\Pages;

use App\Models\AboutUs;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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

/**
 * Single settings page for the About Us content (Deskripsi, Visi, Misi,
 * Value) — there is always exactly one AboutUs row, so this uses a plain
 * form bound to it instead of a list/create/delete Resource.
 */
class ManageAboutUs extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'About Us';

    protected static ?string $title = 'About Us';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $aboutUs = AboutUs::singleton();

        $this->form->fill([
            ...$aboutUs->only([
                'company_name',
                'description_id',
                'description_en',
                'vision_id',
                'vision_en',
                'mission_id',
                'mission_en',
            ]),
            'values' => $aboutUs->values()->get()->map->only(['id', 'title', 'description_id', 'description_en', 'order'])->all(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Deskripsi')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company name')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('description_id')
                            ->label('Description (Indonesian)')
                            ->required(),
                        RichEditor::make('description_en')
                            ->label('Description (English)')
                            ->required(),
                    ]),
                Section::make('Visi & Misi')
                    ->schema([
                        Textarea::make('vision_id')->label('Vision (Indonesian)')->required()->rows(3),
                        Textarea::make('vision_en')->label('Vision (English)')->required()->rows(3),
                        Textarea::make('mission_id')->label('Mission (Indonesian)')->required()->rows(3),
                        Textarea::make('mission_en')->label('Mission (English)')->required()->rows(3),
                    ])
                    ->columns(2),
                Section::make('Value')
                    ->schema([
                        Repeater::make('values')
                            ->schema([
                                TextInput::make('title')->required(),
                                TextInput::make('description_id')->label('Description (Indonesian)')->required(),
                                TextInput::make('description_en')->label('Description (English)')->required(),
                            ])
                            ->columns(3)
                            ->addActionLabel('Add value')
                            ->reorderableWithButtons()
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $values = $data['values'] ?? [];
        unset($data['values']);

        $aboutUs = AboutUs::singleton();
        $aboutUs->update($data);

        $keepIds = [];
        foreach ($values as $i => $value) {
            $record = $aboutUs->values()->updateOrCreate(
                ['id' => $value['id'] ?? null],
                [
                    'title' => $value['title'],
                    'description_id' => $value['description_id'],
                    'description_en' => $value['description_en'],
                    'order' => $i,
                ],
            );
            $keepIds[] = $record->id;
        }
        $aboutUs->values()->whereNotIn('id', $keepIds)->delete();

        Notification::make()
            ->title('About Us saved')
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
