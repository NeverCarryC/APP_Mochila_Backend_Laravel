<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BackpackResource\Pages;
use App\Filament\Resources\BackpackResource\RelationManagers;
use App\Models\Backpack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Fieldset;

class BackpackResource extends Resource
{
    protected static ?string $model = Backpack::class;

    protected static ?string $navigationIcon = 'bi-backpack2-fill';
    protected static ?string $navigationGroup = 'General';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Select::make('trip_id')
                    ->relationship('trip', 'name')
                    ->required(),
                Forms\Components\TextInput::make('color_id')
                    ->required()
                    ->numeric()
                    ->default(1),
                    Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                ])->columns(2);
                
                
                
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trip.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->striped()
            ->actions([
                 Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBackpacks::route('/'),
            'create' => Pages\CreateBackpack::route('/create'),
            'edit' => Pages\EditBackpack::route('/{record}/edit'),
        ];
    }
}
