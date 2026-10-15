<?php

namespace App\Filament\Resources\HeroSlides;

use App\Filament\Resources\HeroSlides\Pages\ManageHeroSlides;
use App\Models\HeroSlide;
use App\Models\HomePage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Slides of the Home page hero carousel (HeroSlider on the frontend).
 */
class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Hero Slides';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TextInput::make('tag_id')->label('Tag (Indonesian)')->maxLength(100),
                        TextInput::make('tag_en')->label('Tag (English)')->maxLength(100),
                        Textarea::make('title_id')->label('Title (Indonesian)')->required()->maxLength(255)->rows(2),
                        Textarea::make('title_en')->label('Title (English)')->required()->maxLength(255)->rows(2),
                        Textarea::make('description_id')->label('Description (Indonesian)')->maxLength(1000)->rows(3),
                        Textarea::make('description_en')->label('Description (English)')->maxLength(1000)->rows(3),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Button & image')
                    ->schema([
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
                            ->directory('hero-slides'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active (shown on the site)')
                    ->default(true),
                TextInput::make('order')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Smaller number shows first.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                ImageColumn::make('image_path')->label('Image'),
                TextColumn::make('tag_id')->label('Tag'),
                TextColumn::make('title_id')->label('Title')->limit(60)->searchable(['title_id', 'title_en']),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('order')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHeroSlides::route('/'),
        ];
    }
}
