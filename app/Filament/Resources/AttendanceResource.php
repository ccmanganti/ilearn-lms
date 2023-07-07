<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classes;
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
use \Saadj55\FilamentCopyable\Tables\Columns\CopyableTextColumn;
use Filament\Forms\Components\CheckboxList;

function myClasses(){
    $classes = Classes::where('uid', auth()->user()->id)->get();
    $arrClasses = [];
    foreach($classes as $class){
        $newArr = array($class->code => $class->name);
        $arrClasses = array_merge($arrClasses, $newArr);
    }
    return $arrClasses;
}

// function myStudents($class){
//     $students = Student::where('code', $class)->where('uid', auth()->user)->first();
//     $arrStudents = [];
//     foreach($students as $student){
//         $newArr = array($student->code => $student->name);
//         $arrStudents = array_merge($arrStudents, $newArr);
//     }
//     return $arrStudents;
// }

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Class Records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('classcode')
                    ->options(
                        myClasses()
                    ) 
                    ->label("Class")
                    ->required()
                    ->columnSpan(2)
                    ->reactive()
                    ->preload(),
                Card::make()
                    ->schema([
                        CheckboxList::make('attendance')
                            ->options(
                                function (callable $get){
                                    $students = Student::where('code', $get('classcode'))->where('uid', auth()->user()->id)->get();
                                    $arrStudents = [];
                                    foreach($students as $student){
                                        $newArr = array($student->code => $student->name);
                                        $arrStudents = array_merge($arrStudents, $newArr);
                                    }
                                    return $arrStudents;
                                }
                            )
                            ->columns(2)
                            ->columnSpan(2)
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('classcode')->searchable()->sortable()->toggleable()->label("Attendance for Class")->description(function(Attendance $record){
                    return Classes::where('code', $record->classcode)->first()->name;
                }),
                TextColumn::make('created_at')->searchable()->sortable()->toggleable()->datetime("F d, Y - h : i A")->label("Date"),
                
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }    

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()->whereHas('attendanceByUser', function (Builder $query){
            $query->where('uid', auth()->user()->id);
        });
        
    }
}
