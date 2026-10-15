<?php

namespace App\Filament\Pages\Halaman;

use App\Models\AboutUs;
use App\Models\AboutValue;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Milestone;
use App\Models\MilestoneEvent;
use App\Models\Partner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

/**
 * Editor for the public About Us page (Tentang Kami): Deskripsi, Visi &
 * Misi, Value, Milestones, Partner, Client — all in one tabbed form.
 */
class ManageAboutUs extends WebsitePageEditor
{
    public const PUBLIC_PATH = '/about';

    protected static ?string $title = 'Tentang Kami';

    protected static ?string $slug = 'halaman/tentang-kami';

    public static function lastUpdatedAt(): ?Carbon
    {
        return collect([AboutUs::class, AboutValue::class, Milestone::class, MilestoneEvent::class, Partner::class, ClientCategory::class, Client::class])
            ->map(fn (string $model) => $model::query()->max('updated_at'))
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->max();
    }

    public function mount(): void
    {
        $aboutUs = AboutUs::singleton();

        $this->form->fill([
            ...$aboutUs->only(['company_name', 'description_id', 'description_en', 'vision_id', 'vision_en', 'mission_id', 'mission_en']),
            'values' => $aboutUs->values()->get()->map->only(['id', 'title', 'description_id', 'description_en'])->all(),
            'milestones' => Milestone::with('events')->orderByDesc('order')->get()
                ->map(fn (Milestone $m) => [
                    'id' => $m->id,
                    'year' => $m->year,
                    'events' => $m->events->map->only(['id', 'text_id', 'text_en'])->all(),
                ])->all(),
            'partners' => Partner::query()->orderBy('order')->get()->map->only(['id', 'name', 'logo_path', 'website_url'])->all(),
            'client_categories' => ClientCategory::with('clients')->orderBy('order')->get()
                ->map(fn (ClientCategory $c) => [
                    'id' => $c->id,
                    'name_id' => $c->name_id,
                    'name_en' => $c->name_en,
                    'clients' => $c->clients->map->only(['id', 'name', 'logo_path', 'website_url'])->all(),
                ])->all(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Tentang Kami')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Deskripsi')
                            ->schema([
                                TextInput::make('company_name')->label('Company name')->required()->maxLength(255),
                                RichEditor::make('description_id')->label('Description (Indonesian)')->required(),
                                RichEditor::make('description_en')->label('Description (English)')->required(),
                            ]),
                        Tab::make('Visi & Misi')
                            ->schema([
                                Textarea::make('vision_id')->label('Vision (Indonesian)')->required()->rows(3),
                                Textarea::make('vision_en')->label('Vision (English)')->required()->rows(3),
                                Textarea::make('mission_id')->label('Mission (Indonesian)')->required()->rows(3),
                                Textarea::make('mission_en')->label('Mission (English)')->required()->rows(3),
                            ])
                            ->columns(2),
                        Tab::make('Value')
                            ->schema([
                                Repeater::make('values')
                                    ->hiddenLabel()
                                    ->schema([
                                        Hidden::make('id'),
                                        TextInput::make('title')->required(),
                                        TextInput::make('description_id')->label('Description (Indonesian)')->required(),
                                        TextInput::make('description_en')->label('Description (English)')->required(),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Add value')
                                    ->reorderableWithButtons()
                                    ->defaultItems(0),
                            ]),
                        Tab::make('Milestones')
                            ->schema([
                                Repeater::make('milestones')
                                    ->hiddenLabel()
                                    ->helperText('Top item shows first on the site (most recent year on top).')
                                    ->schema([
                                        Hidden::make('id'),
                                        TextInput::make('year')->required()->maxLength(10),
                                        Repeater::make('events')
                                            ->schema([
                                                Hidden::make('id'),
                                                TextInput::make('text_id')->label('Event (Indonesian)')->required(),
                                                TextInput::make('text_en')->label('Event (English)')->required(),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('Add event')
                                            ->reorderableWithButtons()
                                            ->defaultItems(1),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['year'] ?? null)
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Add milestone')
                                    ->reorderableWithButtons()
                                    ->defaultItems(0),
                            ]),
                        Tab::make('Partner')
                            ->schema([
                                Repeater::make('partners')
                                    ->hiddenLabel()
                                    ->schema([
                                        Hidden::make('id'),
                                        TextInput::make('name')->required()->maxLength(255),
                                        TextInput::make('website_url')->label('Website URL')->url()->maxLength(255),
                                        FileUpload::make('logo_path')->label('Logo')->image()->disk('public')->directory('partners')->required(),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->collapsible()
                                    ->addActionLabel('Add partner')
                                    ->reorderableWithButtons()
                                    ->defaultItems(0),
                            ]),
                        Tab::make('Client')
                            ->schema([
                                Repeater::make('client_categories')
                                    ->label('Client categories')
                                    ->helperText('Each category is a tab on the About Us page, in this order.')
                                    ->schema([
                                        Hidden::make('id'),
                                        TextInput::make('name_id')->label('Name (Indonesian)')->required()->maxLength(255),
                                        TextInput::make('name_en')->label('Name (English)')->required()->maxLength(255),
                                        Repeater::make('clients')
                                            ->schema([
                                                Hidden::make('id'),
                                                TextInput::make('name')->required()->maxLength(255),
                                                TextInput::make('website_url')->label('Website URL')->url()->maxLength(255),
                                                FileUpload::make('logo_path')->label('Logo')->image()->disk('public')->directory('clients')->required(),
                                            ])
                                            ->columns(3)
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                            ->collapsible()
                                            ->addActionLabel('Add client')
                                            ->reorderableWithButtons()
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['name_id'] ?? null)
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Add category')
                                    ->reorderableWithButtons()
                                    ->defaultItems(0),
                            ]),
                    ]),
            ]);
    }

    protected function persist(array $data): void
    {
        $aboutUs = AboutUs::singleton();
        $aboutUs->update(collect($data)->only($aboutUs->getFillable())->all());

        $this->syncRecords(AboutValue::class, $data['values'] ?? [], fn (array $v, int $i) => [
            'title' => $v['title'],
            'description_id' => $v['description_id'],
            'description_en' => $v['description_en'],
            'order' => $i,
        ], ['about_us_id' => $aboutUs->id]);

        // The API lists milestones by `order` descending, so the top item
        // in the editor gets the largest number.
        $milestones = array_values($data['milestones'] ?? []);
        $saved = $this->syncRecords(Milestone::class, $milestones, fn (array $m, int $i) => [
            'year' => $m['year'],
            'order' => count($milestones) - $i,
        ]);
        foreach ($saved as $i => $milestone) {
            $this->syncRecords(MilestoneEvent::class, $milestones[$i]['events'] ?? [], fn (array $e, int $j) => [
                'text_id' => $e['text_id'],
                'text_en' => $e['text_en'],
                'order' => $j,
            ], ['milestone_id' => $milestone->id]);
        }

        $this->syncRecords(Partner::class, $data['partners'] ?? [], fn (array $p, int $i) => [
            'name' => $p['name'],
            'logo_path' => $p['logo_path'],
            'website_url' => $p['website_url'] ?? null,
            'order' => $i,
        ]);

        $categories = array_values($data['client_categories'] ?? []);
        $saved = $this->syncRecords(ClientCategory::class, $categories, fn (array $c, int $i) => [
            'name_id' => $c['name_id'],
            'name_en' => $c['name_en'],
            'order' => $i,
        ]);
        foreach ($saved as $i => $category) {
            $this->syncRecords(Client::class, $categories[$i]['clients'] ?? [], fn (array $cl, int $j) => [
                'name' => $cl['name'],
                'logo_path' => $cl['logo_path'],
                'website_url' => $cl['website_url'] ?? null,
                'order' => $j,
            ], ['client_category_id' => $category->id]);
        }
    }
}
