<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Assignments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $navigationGroup = 'Class';

    protected static string $view = 'filament.pages.assignments';

}
