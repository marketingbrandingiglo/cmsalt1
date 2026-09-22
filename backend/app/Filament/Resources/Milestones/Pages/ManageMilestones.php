<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Resources\Milestones\MilestoneResource;
use App\Models\AboutUs;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;

/**
 * The milestone list, with the "Milestones" section heading (title/
 * description shown above the timeline on the About Us page) editable at
 * the top — kept on this page instead of the Visi Misi settings page so
 * everything about Milestones lives in one place.
 */
class ManageMilestones extends ManageRecords
{
    protected static string $resource = MilestoneResource::class;

    /** @var array<string, mixed>|null */
    public ?array $headerData = [];

    public function mount(): void
    {
        parent::mount();

        $this->headerForm->fill(
            AboutUs::singleton()->only([
                'milestone_title_id',
                'milestone_title_en',
                'milestone_description_id',
                'milestone_description_en',
            ]),
        );
    }

    public function headerForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('headerData')
            ->components([
                Section::make('Milestones')
                    ->description('Title and description shown above the milestone list on the About Us page.')
                    ->schema([
                        TextInput::make('milestone_title_id')->label('Title (Indonesian)')->maxLength(255),
                        TextInput::make('milestone_title_en')->label('Title (English)')->maxLength(255),
                        Textarea::make('milestone_description_id')->label('Description (Indonesian)')->rows(3),
                        Textarea::make('milestone_description_en')->label('Description (English)')->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public function saveHeader(): void
    {
        AboutUs::singleton()->update($this->headerForm->getState());

        Notification::make()
            ->title('Milestones section saved')
            ->success()
            ->send();
    }

    protected function getHeaderFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('headerForm')])
            ->id('headerForm')
            ->livewireSubmitHandler('saveHeader')
            ->footer([
                Actions::make([
                    Action::make('saveHeader')
                        ->label('Save')
                        ->submit('saveHeader'),
                ])->key('header-form-actions'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getHeaderFormContentComponent(),
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
