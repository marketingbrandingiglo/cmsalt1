<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Halaman\ManageAboutUs;
use App\Filament\Pages\Halaman\ManageHome;
use App\Filament\Pages\Halaman\WebsitePageEditor;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

/**
 * The single "Halaman" (Pages) entry in the admin sidebar: lists every
 * public website page; each row opens that page's editor. To add a page,
 * create a WebsitePageEditor subclass and add it to PAGES.
 */
class Halaman extends Page implements HasTable
{
    use InteractsWithTable;

    /** @var list<class-string<WebsitePageEditor>> in display order */
    public const PAGES = [
        ManageHome::class,
        ManageAboutUs::class,
    ];

    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Halaman';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Halaman';

    protected static ?string $slug = 'halaman';

    /** Keep "Halaman" highlighted while editing one of its pages. */
    public static function getNavigationItemActiveRoutePattern(): string|array
    {
        return [static::getRouteName(), static::getRouteName().'.*'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => collect(self::PAGES)
                ->mapWithKeys(fn (string $page, int $i) => [$i + 1 => [
                    'title' => $page::getPageLabel(),
                    'path' => $page::PUBLIC_PATH,
                    'updated_at' => $page::lastUpdatedAt(),
                    'url' => $page::getUrl(),
                ]])
                ->all())
            ->columns([
                TextColumn::make('title')->label('Halaman')->weight('bold'),
                TextColumn::make('path')->label('URL situs')->color('gray'),
                TextColumn::make('updated_at')->label('Terakhir diubah')->since()->placeholder('—'),
            ])
            ->recordUrl(fn (array $record): string => $record['url'])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (array $record): string => $record['url']),
            ])
            ->paginated(false);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([EmbeddedTable::make()]);
    }
}
