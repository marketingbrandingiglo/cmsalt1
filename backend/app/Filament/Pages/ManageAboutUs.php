<?php

namespace App\Filament\Pages;

use App\Models\AboutUs;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
                'banner_image_path',
                'banner_title_id',
                'banner_title_en',
                'banner_description_id',
                'banner_description_en',
                'description_id',
                'description_en',
                'vision_id',
                'vision_en',
                'mission_id',
                'mission_en',
                'milestone_title_id',
                'milestone_title_en',
                'milestone_description_id',
                'milestone_description_en',
                'video_title_id',
                'video_title_en',
                'video_description_id',
                'video_description_en',
                'video_youtube_url',
            ]),
            'stats' => $aboutUs->stats()->get()->map->only(['id', 'value', 'label_id', 'label_en', 'note_id', 'note_en', 'icon_path', 'order'])->all(),
        ]);
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
                            ->directory('about-banner'),
                        TextInput::make('banner_title_id')->label('Title (Indonesian)')->maxLength(255),
                        TextInput::make('banner_title_en')->label('Title (English)')->maxLength(255),
                        Textarea::make('banner_description_id')->label('Description (Indonesian)')->rows(3),
                        Textarea::make('banner_description_en')->label('Description (English)')->rows(3),
                    ])
                    ->columns(2),
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
                Section::make('Vision & Mission Stats')
                    ->schema([
                        Repeater::make('stats')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Value')
                                    ->required()
                                    ->helperText('e.g. 50, 1100'),
                                TextInput::make('label_id')->label('Label (Indonesian)')->required(),
                                TextInput::make('label_en')->label('Label (English)')->required(),
                                TextInput::make('note_id')->label('Note (Indonesian)'),
                                TextInput::make('note_en')->label('Note (English)'),
                                FileUpload::make('icon_path')
                                    ->label('Icon/SVG')
                                    ->image()
                                    ->disk('public')
                                    ->directory('about-stats'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add stat')
                            ->reorderableWithButtons()
                            ->defaultItems(0),
                    ]),
                Section::make('Milestones')
                    ->description('Section heading shown above the milestone timeline. Manage the timeline itself under the Milestones menu.')
                    ->schema([
                        TextInput::make('milestone_title_id')->label('Title (Indonesian)')->maxLength(255),
                        TextInput::make('milestone_title_en')->label('Title (English)')->maxLength(255),
                        Textarea::make('milestone_description_id')->label('Description (Indonesian)')->rows(3),
                        Textarea::make('milestone_description_en')->label('Description (English)')->rows(3),
                    ])
                    ->columns(2),
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
                    'icon_path' => $stat['icon_path'] ?? null,
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
