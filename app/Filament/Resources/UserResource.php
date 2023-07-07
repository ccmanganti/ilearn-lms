<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Role;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Closure;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class UserResource extends Resource
{
    protected static ?int $navigationSort = 1;
    
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'heroicon-o-user';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignorable: fn ($record) => $record),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignorable: fn ($record) => $record),
                TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Select::make('roles')
                    ->preload()
                    ->reactive()
                    ->required()
                    ->multiple(1)
                    ->relationship('roles', 'name')
                    ->options(function() {
                        if (auth()->user()->getRoleNames()->first() === 'Superadmin'){
                            return Role::all()->pluck('name', 'id');
                        } elseif (auth()->user()->getRoleNames()->first() === 'Instructor') {
                            return Role::where([['name', '!=', 'Superadmin'], ['name', '!=', 'Instructor']])->pluck('name', 'id');
                        }
                    }),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->toggleable(),
                TextColumn::make('email')->searchable()->sortable()->toggleable()->icon('heroicon-s-inbox')->default('Undefined'),
                TextColumn::make('roles.name')
                    ->searchable()->sortable()->toggleable()->color(function(User $record){
                        if($record->hasRole('Superadmin')){
                            return "danger";
                        } else if($record->hasRole('Instructor')){
                            return "primary";
                        } else if($record->hasRole('Student')){
                            return "secondary";
                        }
                    })->icon('heroicon-s-user')->size('lg'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
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

    // public static function getEloquentQuery(): Builder
    // {
    //     $user = auth()->user();
    //     if ($user->getRoleNames()->first() === 'Superadmin'){
    //         return parent::getEloquentQuery()->where('name', '!=', '');
    //     } elseif($user->getRoleNames()->first() === 'Instructor') {
    //         return parent::getEloquentQuery()->where('name', '!=', '')
    //             ->whereHas('roles', function (Builder $query) {
    //             $query->where('name', '!=', 'Superadmin')
    //                 ->where('name', '!=', 'Instructor');
    //         });
    //     }
    // }
}
