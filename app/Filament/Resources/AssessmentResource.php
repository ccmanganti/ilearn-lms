<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Filament\Resources\AssessmentResource\RelationManagers;
use App\Models\Assessment;
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
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use \Saadj55\FilamentCopyable\Tables\Columns\CopyableTextColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use \Datetime;
use \DateTimeZone;

class AssessmentResource extends Resource
{
    protected static ?int $navigationSort = 6;

    protected static ?string $model = Assessment::class;

    protected static ?string $navigationGroup = 'Resource Management';
    protected static ?string $navigationIcon = 'heroicon-s-archive';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class')
                ->options(
                    classFunc()
                ) 
                ->required()
                ->preload()
                ->columnSpan(2),
                TextInput::make('title')
                ->required()
                ->maxLength(50)
                ->unique(ignorable: fn ($record) => $record),
                DateTimePicker::make('due')
                ->required()
                ->minDate(now()->subYears(0)),
                RichEditor::make('desc')
                ->required()
                ->label("Exam Description")
                ->fileAttachmentsDisk('local')
                ->fileAttachmentsDirectory('public')->columnSpan(2),
                
                Repeater::make('item')
                ->schema([
                    Select::make('type')
                    ->options([
                            't1' => 'Multiple Choice',
                            't2' => 'Identification',
                    ])
                    ->reactive()
                    ->label("Item Type")
                    ->required()
                    ->preload()
                    ->columnSpan(2),
                    TextInput::make('choicenum')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->default(1)
                        ->minvalue(1)
                        ->maxLength(10)
                        ->columnSpan(2)
                        ->hidden(fn (Closure $get) => $get('type') == 't2' || $get('type') == null)
                        ->label("Max Number of Choices"),
                    TextInput::make('question')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    Repeater::make('choices')
                    ->schema([
                        TextInput::make('choice')
                        ->required()
                        ->maxLength(255)
                    ])
                    ->minItems(fn (Closure $get) => (int)$get('choicenum'))
                    ->maxItems(fn (Closure $get) => (int)$get('choicenum'))
                    ->columnSpan(2)
                    ->hidden(fn (Closure $get) => $get('type') == 't2' || $get('type') == null),
                    TextInput::make('answermc')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2)
                        ->label("Answer")
                        ->hidden(fn (Closure $get) => $get('type') != 't2' || $get('type') == null),
                    Select::make('answerid')
                        ->label("Answer")
                        ->columnSpan(2)
                        ->options(function(callable $get){
                            $arrClasses = [];
                            for($num = 1; $num <= $get("choicenum"); $num++){
                                $newArr = array($num => "Choice ".$num);
                                $arrClasses = array_merge($arrClasses, $newArr);
                            }
                            return $arrClasses;
                        })
                        ->hidden(fn (Closure $get) => $get('type') == 't2' || $get('type') == null)
                ])
                ->label("Question Items")
                ->columnSpan(2)
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->toggleable()->label("Title"),
                TextColumn::make('class')->searchable()->sortable()->toggleable()->label("Posted on Class")->formatStateUsing(function(string $state){
                    return str_replace(array( '[', ']', '"' ), ' ', $state);
                }),
                TextColumn::make('due')->datetime()->toggleable(isToggledHiddenByDefault: false)->label("Due Date")->description(function(Assessment $record){

                    $datetime1 = (new DateTime("now"))->setTimeZone(new DateTimeZone('Asia/Manila'));
                    $datetime2 = new DateTime($record->due);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = (new DateTime($record->due))->format('%y %m %a %h %i %s');
                    
                    if ($datetime1 < $datetime2){
                        if(($datetime2)->format('H') < ($datetime1)->format('H')){
                            return "Overdue";
                            // return ($datetime2)->format('H').($datetime1)->format('H');
                        } else{
                            if((($datetime2)->format('H') == ($datetime1)->format('H')) && (($datetime2)->format('i') < ($datetime1)->format('i'))){
                                return "Overdue";
                            }
                            return "Accepting Responses";
                        }
                    } else {
                        return "Overdue";

                    }


                })->color(function(Assessment $record){
                    $datetime1 = (new DateTime("now"))->setTimeZone(new DateTimeZone('Asia/Manila'));
                    $datetime2 = new DateTime($record->due);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = (new DateTime($record->due))->format('%y %m %a %h %i %s');
                    
                    if ($datetime1 < $datetime2){
                        if(($datetime2)->format('H') < ($datetime1)->format('H')){
                            return "danger";
                        } else{
                            if((($datetime2)->format('H') == ($datetime1)->format('H')) && (($datetime2)->format('i') < ($datetime1)->format('i'))){
                                return "danger";
                            }
                            return "primary";
                        }
                    } else {
                        return "danger";

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
            'index' => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'edit' => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()->whereHas('assessmentByUser', function (Builder $query){
            $query->where('uid', auth()->user()->id);
        });
        
    }
}
