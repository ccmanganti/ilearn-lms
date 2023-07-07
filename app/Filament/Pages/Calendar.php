<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CalendarWidget;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.calendar';

    protected static ?string $navigationGroup = 'Class';

    protected static ?int $navigationSort = 3;


    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class
        ];
    }
}
