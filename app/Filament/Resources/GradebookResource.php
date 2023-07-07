<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradebookResource\Pages;
use App\Filament\Resources\GradebookResource\RelationManagers;
use App\Models\Score;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Role;
use App\Models\Classes;
use App\Models\Task;
use App\Models\User;
use App\Models\Assessment;
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
use \Saadj55\FilamentCopyable\Tables\Columns\CopyableTextColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

function myAssignments(){
    $task = Task::where('uid', auth()->user()->id)->get();
    $ass = Assessment::where('uid', auth()->user()->id)->get();
    
    $assClasses = [];
    
    foreach($ass as $as){
        $newAss = array($as->title => $as->title);
        $assClasses = array_merge($assClasses, $newAss);
    }
    foreach($task as $ta){
        $newTa = array($ta->title => $ta->title);
        $assClasses = array_merge($assClasses, $newTa);
    }
    return $assClasses;
}

class GradebookResource extends Resource
{
    protected static ?string $model = Score::class;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Gradebook';

    protected static ?string $slug = 'gradebook';

    protected static ?string $navigationGroup = 'Class Records';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static function shouldRegisterNavigation(): bool
    {
        if(auth()->user()->hasRole('Student')){
            return false;
        }
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')->searchable()->sortable()->toggleable()->label("Name"),
                TextColumn::make('classname')->searchable()->sortable()->toggleable()->label("Class"),
                TextColumn::make('classprofid')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)->label("Instructor")->description(function(Score $record){
                    return User::where('id', $record->classprofid)->first()->name;
                }),
                TextColumn::make('assname')->searchable()->sortable()->toggleable()->label("Assignment"),
                TextColumn::make('score')->searchable()->sortable()->toggleable()->label("Score"),
                TextColumn::make('asspoints')->searchable()->sortable()->toggleable()->label("Points"),
            ])
            ->filters([
                SelectFilter::make('class-name')
                    ->options(
                        myClasses()
                    )
                    ->attribute('classcode'),
                SelectFilter::make('assignment-type')
                    ->options([
                        "Task" => "Task",
                        "Assessment" => "Assessment",
                    ])
                    ->attribute('asstype'),
                SelectFilter::make('assignment')
                    ->options(
                        myAssignments()
                    )
                    ->attribute('assname'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListGradebooks::route('/'),
            'create' => Pages\CreateGradebook::route('/create'),
            'edit' => Pages\EditGradebook::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('studentByUser', function (Builder $query){
            $query->where('id', auth()->user()->id);
        });
        
    }
}
