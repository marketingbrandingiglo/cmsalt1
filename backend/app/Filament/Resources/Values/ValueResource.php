<?php

namespace App\Filament\Resources\Values;

use App\Filament\Resources\Values\Pages\ManageValues;
use App\Models\AboutValue;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Company values (Value I5) shown as cards on the About Us page. Kept as
 * its own resource, separate from the Vision & Mission settings page —
 * there is always exactly one owning AboutUs row, set automatically (see
 * AboutValue::booted()).
 */
class ValueResource extends Resource
{
    protected static ?string $model = AboutValue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Values';

    protected static string|UnitEnum|null $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title/Name')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->directory('about-values'),
                Textarea::make('description_id')
                    ->label('Description (Indonesian)')
                    ->rows(3),
                Textarea::make('description_en')
                    ->label('Description (English)')
                    ->rows(3),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                ImageColumn::make('image_path')->label('Image')->disk('public'),
                TextColumn::make('title')->searchable(),
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
            'index' => ManageValues::route('/'),
        ];
    }
}
