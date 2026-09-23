<?php

namespace App\Filament\Pages;

use App\Models\AboutUs;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
 * Single settings page for the About Us content (Deskripsi, Visi, Misi,
 * Vision & Mission Stats, Company Video) — there is always exactly one
 * AboutUs row, so this uses a plain form bound to it instead of a
 * list/create/delete Resource. Banner, Values, and the Milestones section
 * heading are managed on their own pages/resources instead.
 */
class ManageAboutUs extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Visi Misi';

    protected static ?string $title = 'Visi Misi';

    protected static string|UnitEnum|null $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $aboutUs = AboutUs::singleton();

        $this->form->fill([
            ...$aboutUs->only([
                'company_name',
                'description_image_path',
                'description_id',
                'description_en',
                'vision_id',
                'vision_en',
                'mission_id',
                'mission_en',
                'video_title_id',
                'video_title_en',
                'video_description_id',
                'video_description_en',
                'video_youtube_url',
            ]),
            'stats' => $aboutUs->stats()->get()->map->only(['id', 'value', 'label_id', 'label_en', 'note_id', 'note_en', 'icon', 'order'])->all(),
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
                        FileUpload::make('description_image_path')
                            ->label('Image')
                            ->helperText('Shown beside the Vision & Mission text on the frontend (the "i5" graphic).')
                            ->image()
                            ->disk('public')
                            ->directory('about-description'),
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
                Section::make('Vision & Mission Stats')
                    ->description('Each entry shows as two cards on the frontend: a number+label counter, and an icon+text callout next to it.')
                    ->schema([
                        Repeater::make('stats')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Number')
                                    ->required()
                                    ->helperText('e.g. 50, 1100 — shown as "More Than {number}".'),
                                TextInput::make('label_id')->label('Number label (Indonesian)')->required(),
                                TextInput::make('label_en')->label('Number label (English)')->required(),
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options([
                                        'speed' => 'Lightning (speed/performance)',
                                        'layers' => 'Layers (digital transformation)',
                                    ])
                                    ->native(false),
                                TextInput::make('note_id')->label('Icon callout text (Indonesian)'),
                                TextInput::make('note_en')->label('Icon callout text (English)'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add stat')
                            ->reorderableWithButtons()
                            ->defaultItems(0),
                    ]),
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
        $data = $this->form->getState();
        $stats = $data['stats'] ?? [];
        unset($data['stats']);

        $aboutUs = AboutUs::singleton();
        $aboutUs->update($data);

        $keepIds = [];
        foreach ($stats as $i => $stat) {
            $record = $aboutUs->stats()->updateOrCreate(
                ['id' => $stat['id'] ?? null],
                [
                    'value' => $stat['value'],
                    'label_id' => $stat['label_id'],
                    'label_en' => $stat['label_en'],
                    'note_id' => $stat['note_id'] ?? null,
                    'note_en' => $stat['note_en'] ?? null,
                    'icon' => $stat['icon'] ?? null,
                    'order' => $i,
                ],
            );
            $keepIds[] = $record->id;
        }
        $aboutUs->stats()->whereNotIn('id', $keepIds)->delete();

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
