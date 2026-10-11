<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditPackageResource\Pages;
use App\Models\CreditPackage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditPackageResource extends Resource
{
    protected static ?string $model = CreditPackage::class;
    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('price_paise')->numeric()->required(),
            TextInput::make('credits')->numeric()->required(),
            TextInput::make('bonus_credits')->numeric()->default(0),
            TextInput::make('valid_days')->numeric()->required(),
            TextInput::make('badge'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('price_paise')->money('INR', divideBy: 100),
            Tables\Columns\TextColumn::make('credits'),
            Tables\Columns\TextColumn::make('bonus_credits'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListCreditPackages::route('/')];
    }
}
