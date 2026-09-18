<?php

namespace App\Filament\Resources\ClientCategories;

use App\Filament\Resources\ClientCategories\Pages\ManageClientCategories;
use App\Models\ClientCategory;
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

class ClientCategoryResource extends Resource
{
    protected static ?string $model = ClientCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Client Categories';

    protected static string|UnitEnum|null $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_id')
                    ->label('Name (Indonesian)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name_en')
                    ->label('Name (English)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Controls the tab order on the About Us page.'),
                Repeater::make('clients')
                    ->relationship('clients')
                    ->orderColumn('order')
                    ->schema([
                        TextInput::make('name')
                            ->label('Client name')
                            ->required(),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('clients'),
                        TextInput::make('website_url')
                            ->label('Website URL')
                            ->url(),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add client')
                    ->reorderableWithButtons()
                    ->defaultItems(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                TextColumn::make('name_id')->label('Name (ID)')->searchable(),
                TextColumn::make('name_en')->label('Name (EN)')->searchable(),
                TextColumn::make('clients_count')->counts('clients')->label('Clients'),
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
            'index' => ManageClientCategories::route('/'),
        ];
    }
}
