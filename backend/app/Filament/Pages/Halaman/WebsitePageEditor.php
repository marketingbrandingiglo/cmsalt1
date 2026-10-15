<?php

namespace App\Filament\Pages\Halaman;

use App\Filament\Pages\Halaman;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Base for one public website page's editor (Beranda, Tentang Kami, …).
 * Each page is a single form — its sections are tabs, list-type content
 * (slides, milestones, partners, …) is edited inline as repeaters — opened
 * from the "Halaman" list instead of having its own sidebar entries.
 */
abstract class WebsitePageEditor extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static bool $shouldRegisterNavigation = false;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** The page's name in the "Halaman" list (same as its title). */
    public static function getPageLabel(): string
    {
        return static::$title;
    }

    /** When this page's content was last changed, for the "Halaman" list. */
    abstract public static function lastUpdatedAt(): ?Carbon;

    abstract protected function persist(array $data): void;

    public function save(): void
    {
        $this->persist($this->form->getState());

        Notification::make()
            ->title($this->getTitle().' saved')
            ->success()
            ->send();
    }

    /**
     * Upserts the repeater $items into $model rows (scoped by $scope, e.g. a
     * parent foreign key), deletes the rows that were removed, and returns
     * the saved records in repeater order.
     *
     * @param  class-string<Model>  $model
     * @param  callable(array, int): array  $attributes  item + index → columns
     * @return list<Model>
     */
    protected function syncRecords(string $model, array $items, callable $attributes, array $scope = []): array
    {
        $saved = [];

        foreach (array_values($items) as $i => $item) {
            $record = $model::query()->where($scope)->find($item['id'] ?? null) ?? new $model($scope);
            $record->forceFill($scope)->fill($attributes($item, $i))->save();
            $saved[] = $record;
        }

        $model::query()->where($scope)->whereNotIn('id', array_map(fn ($r) => $r->id, $saved))->delete();

        return $saved;
    }

    public function getBreadcrumbs(): array
    {
        return [
            Halaman::getUrl() => Halaman::getNavigationLabel(),
            $this->getTitle(),
        ];
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
