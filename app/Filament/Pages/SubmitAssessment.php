<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SubmitAssessment extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.submit-assessment';

    protected static ?string $slug = 'assess/{code}/{id}';

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
