<?php
 
namespace App\Filament\Pages;
 
use Filament\Pages\Dashboard as BasePage;
use App\Filament\Widgets\StatsOverview;
 
class Dashboard extends BasePage
{
    // ...

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         StatsOverview::class
    //     ];
    // }

    protected static ?string $title = 'My Classes';
    protected static string $view = 'filament.pages.dashboard';

}
