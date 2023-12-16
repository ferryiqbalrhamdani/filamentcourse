<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email(),
                        TextInput::make('password')->password()->visibleOn('create'),
                        Select::make('role')->options([
                            'ADMIN' => 'Admin',
                            'USER' => 'User',
                            'EDITOR' => 'Editor',
                        ])
                            ->searchable()
                            ->preload(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email')->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'ADMIN' => 'danger',
                            'EDITOR' => 'info',
                            'USER' => 'success',
                        };
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->searchable()
                    ->options([
                        'ADMIN' => 'Admin',
                        'EDITOR' => 'Editor',
                        'USER' => 'User',
                    ])
                    ->preload()
                    ->multiple()
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
            CommentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
