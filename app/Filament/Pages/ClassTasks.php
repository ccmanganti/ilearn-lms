<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ClassTasks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.class-tasks';

    protected static ?string $slug = 'myclass/{code}/class-assignments';

    protected static ?string $title = 'Class Assignments';

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
