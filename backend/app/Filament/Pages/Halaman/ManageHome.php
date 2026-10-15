<?php

namespace App\Filament\Pages\Halaman;

use App\Models\HeroSlide;
use App\Models\HomePage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

/**
 * Editor for the public Home page (Beranda): hero slides, Intro/Tagline,
 * and the Products & News section headings.
 */
class ManageHome extends WebsitePageEditor
{
    public const PUBLIC_PATH = '/';

    protected static ?string $title = 'Beranda';

    protected static ?string $slug = 'halaman/beranda';

    public static function lastUpdatedAt(): ?Carbon
    {
        return collect([
            HomePage::query()->max('updated_at'),
            HeroSlide::query()->max('updated_at'),
        ])->filter()->map(fn ($date) => Carbon::parse($date))->max();
    }

    public function mount(): void
    {
        $this->form->fill([
            ...HomePage::singleton()->only(HomePage::FIELDS),
            'hero_slides' => HeroSlide::query()->orderBy('order')->orderBy('id')->get()
                ->map->only(['id', 'tag_id', 'tag_en', 'title_id', 'title_en', 'description_id', 'description_en', 'image_path', 'cta_label_id', 'cta_label_en', 'cta_url', 'is_active'])
                ->all(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Beranda')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Hero Slides')
                            ->schema([
                                Repeater::make('hero_slides')
                                    ->hiddenLabel()
                                    ->schema([
                                        Hidden::make('id'),
                                        TextInput::make('tag_id')->label('Tag (Indonesian)')->maxLength(100),
                                        TextInput::make('tag_en')->label('Tag (English)')->maxLength(100),
                                        Textarea::make('title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                                        Textarea::make('title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                                        Textarea::make('description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
                                        Textarea::make('description_en')->label('Description (English)')->maxLength(1000)->rows(3),
                                        TextInput::make('cta_label_id')->label('Button label (Indonesian)')->maxLength(100),
                                        TextInput::make('cta_label_en')->label('Button label (English)')->maxLength(100),
                                        TextInput::make('cta_url')
                                            ->label('Button link')
                                            ->placeholder('/news or https://...')
                                            ->helperText('Site path starting with "/" or a full http(s) URL. Leave empty to hide the button.')
                                            ->regex(HomePage::LINK_REGEX)
                                            ->maxLength(255),
                                        FileUpload::make('image_path')
                                            ->label('Image (optional)')
                                            ->helperText('Leave empty to use the default illustration.')
                                            ->image()
                                            ->maxSize(4096)
                                            ->disk('public')
                                            ->directory('hero-slides'),
                                        Toggle::make('is_active')
                                            ->label('Active (shown on the site)')
                                            ->default(true),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['title_id'] ?? null)
                                    ->collapsible()
                                    ->addActionLabel('Add slide')
                                    ->reorderableWithButtons()
                                    ->defaultItems(0),
                            ]),
                        Tab::make('Intro')
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
                        Tab::make('Produk & Solusi')
                            ->schema([
                                TextInput::make('products_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
                                TextInput::make('products_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
                                Textarea::make('products_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                                Textarea::make('products_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                                Textarea::make('products_description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
                                Textarea::make('products_description_en')->label('Description (English)')->maxLength(1000)->rows(3),
                            ])
                            ->columns(2),
                        Tab::make('Berita')
                            ->schema([
                                TextInput::make('news_kicker_id')->label('Kicker (Indonesian)')->required()->maxLength(150),
                                TextInput::make('news_kicker_en')->label('Kicker (English)')->required()->maxLength(150),
                                Textarea::make('news_title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                                Textarea::make('news_title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    protected function persist(array $data): void
    {
        $slides = $data['hero_slides'] ?? [];
        unset($data['hero_slides']);

        HomePage::singleton()->update($data);

        $this->syncRecords(HeroSlide::class, $slides, fn (array $slide, int $i) => [
            ...collect($slide)->except('id')->all(),
            'is_active' => (bool) ($slide['is_active'] ?? true),
            'order' => $i,
        ]);
    }
}
