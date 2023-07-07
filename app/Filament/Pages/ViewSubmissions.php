<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ViewSubmissions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.view-submissions';

    protected static ?string $slug = 'myclass/{code}/{type}/{id}';

    protected static ?string $title = 'Manage Submissions';

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
