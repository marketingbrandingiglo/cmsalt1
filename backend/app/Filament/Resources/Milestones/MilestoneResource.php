<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Resources\Milestones\Pages\ManageMilestones;
use App\Models\Milestone;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Milestones';

    protected static string|UnitEnum|null $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('period')
                    ->required()
                    ->maxLength(50)
                    ->helperText('E.g. "2021 - Present".'),
                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Larger number shows first (most recent period on top).'),
                Repeater::make('logos')
                    ->relationship('logos')
                    ->orderColumn('order')
                    ->schema([
                        TextInput::make('name')
                            ->label('Partner/client name')
                            ->required(),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('milestones'),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add logo')
                    ->reorderableWithButtons()
                    ->defaultItems(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'desc')
            ->columns([
                TextColumn::make('period')->sortable(),
                TextColumn::make('order')->sortable(),
                TextColumn::make('logos_count')
                    ->counts('logos')
                    ->label('Logos'),
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
