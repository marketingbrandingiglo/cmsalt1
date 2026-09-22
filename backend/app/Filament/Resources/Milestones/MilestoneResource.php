<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Resources\Milestones\Pages\ManageMilestones;
use App\Models\Milestone;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Milestones';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('year')
                    ->label('Period/Year')
                    ->required()
                    ->maxLength(10),
                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Larger number shows first (most recent year on top).'),
                Repeater::make('events')
                    ->relationship('events')
                    ->orderColumn('order')
                    ->schema([
                        TextInput::make('text_id')
                            ->label('Title (Indonesian)')
                            ->required(),
                        TextInput::make('text_en')
                            ->label('Title (English)')
                            ->required(),
                        Textarea::make('description_id')
                            ->label('Description (Indonesian)')
                            ->rows(2),
                        Textarea::make('description_en')
                            ->label('Description (English)')
                            ->rows(2),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add milestone')
                    ->reorderableWithButtons()
                    ->defaultItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'desc')
            ->columns([
                TextColumn::make('year')->sortable(),
                TextColumn::make('order')->sortable(),
                TextColumn::make('events_count')
                    ->counts('events')
                    ->label('Events'),
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
            'index' => ManageMilestones::route('/'),
        ];
    }
}
