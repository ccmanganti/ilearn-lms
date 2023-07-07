<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MyClass extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.my-class';

    protected static ?string $slug = 'myclass/{code}';

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
