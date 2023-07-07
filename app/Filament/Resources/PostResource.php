<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
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

function classFunc(){
    $classes = Classes::where('uid', auth()->user()->id)->get();
    $arrClasses = [];
    foreach($classes as $class){
        $newArr = array($class->name => $class->name);
        $arrClasses = array_merge($arrClasses, $newArr);
    }
    return $arrClasses;
    
}

class PostResource extends Resource
{
    protected static ?int $navigationSort = 4;

    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'Resource Management';
    protected static ?string $navigationIcon = 'heroicon-s-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class')
                ->options(
                    classFunc()
                )
                ->required()
                ->preload(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignorable: fn ($record) => $record),
                RichEditor::make('desc')
                ->required()
                ->fileAttachmentsDisk('local')
                ->fileAttachmentsDirectory('public')
                ->toolbarButtons([
                    'attachFiles',
                    'bold',
                    'italic',
                    'link',
                    'redo',
                    'strike',
                    'underline',
                    'undo',
                ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->toggleable()->label("Title"),
                TextColumn::make('class')->searchable()->sortable()->size("sm")->toggleable()->words(5)->label("Posted on Class")->formatStateUsing(function(string $state){
                    return str_replace(array( '[', ']', '"' ), ' ', $state);
                })->words(5),
                TextColumn::make('created_at')->date()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')->since()->label('Updated'),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()->whereHas('postByUser', function (Builder $query){
            $query->where('uid', auth()->user()->id);
        });
        
    }
}
