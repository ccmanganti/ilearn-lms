<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
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
use \Datetime;
use \DateTimeZone;

class TaskResource extends Resource
{
    protected static ?int $navigationSort = 5;

    protected static ?string $model = Task::class;

    protected static ?string $navigationGroup = 'Resource Management';
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-check';

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
                ->preload(),
                DateTimePicker::make('due')
                ->required()
                ->minDate(now()->subYears(1)),
                TextInput::make('points')
                ->numeric()                        
                ->required(),
                TextInput::make('title')
                ->required()
                ->maxLength(50)
                ->unique(ignorable: fn ($record) => $record),
                RichEditor::make('desc')
                ->required()
                ->label("Description and Attachments")
                ->fileAttachmentsDisk('local')
                ->fileAttachmentsDirectory('public')->columnSpan(2),
                
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
                TextColumn::make('due')->datetime()->toggleable(isToggledHiddenByDefault: false)->label("Due Date")->description(function(Task $record){
                    date_default_timezone_set('Asia/Manila');
                    $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                    $targetDateTime = strtotime($record->due); // The target datetime in 24-hour format
                    
                    if ($targetDateTime < $currentDateTime){
                        return "Overdue";

                    } else {
                        return "Accepting Response";
                    }


                })->color(function(Task $record){
                    date_default_timezone_set('Asia/Manila');
                    $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                    $targetDateTime = strtotime($record->due); // The target datetime in 24-hour format
                    
                    if ($targetDateTime < $currentDateTime){
                        return "danger";

                    } else {
                        return "primary";
                    }

                })->icon('heroicon-s-user')->size('lg'),
                TextColumn::make('points')->searchable()->sortable()->toggleable()->label("Points"),

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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }    

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()->whereHas('taskByUser', function (Builder $query){
            $query->where('uid', auth()->user()->id);
        });
        
    }
}
