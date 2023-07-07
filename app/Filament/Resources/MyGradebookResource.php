<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyGradebookResource\Pages;
use App\Filament\Resources\MyGradebookResource\RelationManagers;
use App\Models\Score;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Role;
use App\Models\Classes;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Closure;
use App\Models\Task;
use App\Models\Student;
use App\Models\Assessment;
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

function myStudentClasses(){

    $classcodes = Student::where('userid', auth()->user()->id)->get();
    $classes = [];
    foreach ($classcodes as $class) {
        $newClass = Classes::where('code', $class->code)->first();
        array_push($classes, $newClass);
    }
    
    $arrClasses = [];
    foreach($classes as $class){
        $newArr = array($class->code => $class->name);
        $arrClasses = array_merge($arrClasses, $newArr);
    }
    return $arrClasses;
}

function myStudentAssignments(){

    $classcodes = Student::where('userid', auth()->user()->id)->get();
    $classes = [];
    foreach ($classcodes as $class) {
        $newClass = Classes::where('code', $class->code)->first();
        array_push($classes, $newClass);
    }

    $tasks = [];
    $ass = [];
    foreach ($classes as $class) {
        $task = Task::where('class', $class->name)->get();
        foreach ($task as $tas) {
            array_push($tasks, $tas);
        }
    }
    foreach ($classes as $class) {
        $as = Assessment::where('class', $class->name)->get();
        foreach ($as as $sa) {
            array_push($ass, $sa);
        }
    }
    
    $assClasses = [];
    
    foreach($ass as $as){
        $newAss = array($as->title => $as->title);
        $assClasses = array_merge($assClasses, $newAss);
    }
    foreach($tasks as $ta){
        $newTa = array($ta->title => $ta->title);
        $assClasses = array_merge($assClasses, $newTa);
    }
    return $assClasses;
}

class MyGradebookResource extends Resource
{
    protected static ?string $model = Score::class;

    protected static ?string $navigationLabel = 'My Gradebook';

    protected static ?string $slug = 'my-gradebook';

    protected static ?string $navigationGroup = 'Class';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

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
                TextColumn::make('classprofid   ')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)->label("Instructor")->description(function(Score $record){
                    return User::where('id', $record->classprofid)->first()->name;
                }),
                TextColumn::make('assname')->searchable()->sortable()->toggleable()->label("Assignment"),
                TextColumn::make('score')->searchable()->sortable()->toggleable()->label("Score"),
                TextColumn::make('asspoints')->searchable()->sortable()->toggleable()->label("Points"),
            ])
            ->filters([
                SelectFilter::make('class-name')
                    ->options(
                        myStudentClasses()
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
                        myStudentAssignments()
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
            'index' => Pages\ListMyGradebooks::route('/'),
            'create' => Pages\CreateMyGradebook::route('/create'),
            'edit' => Pages\EditMyGradebook::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('myGrades', function (Builder $query){
            $query->where('id', auth()->user()->id);
        });    
    }
}
