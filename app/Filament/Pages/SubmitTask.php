<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SubmitTask extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.submit-task';

    protected static ?string $slug = 'assignments/{code}/{id}';

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
