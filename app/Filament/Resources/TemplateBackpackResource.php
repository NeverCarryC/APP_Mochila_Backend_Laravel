<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateBackpackResource\Pages;
use App\Models\TemplateBackpack;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\TemplateBackpackResource\RelationManagers;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateBackpackResource extends Resource
{
    protected static ?string $model = TemplateBackpack::class;

    protected static ?string $navigationIcon = 'bi-backpack2-fill';
    protected static ?string $navigationGroup = 'Templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
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
            ->actions([
                Tables\Actions\EditAction::make(),
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
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplateBackpacks::route('/'),
            'create' => Pages\CreateTemplateBackpack::route('/create'),
            'edit' => Pages\EditTemplateBackpack::route('/{record}/edit'),
        ];
    }
}
