<?php

namespace App\Filament\Resources\GradebookResource\Pages;

use App\Filament\Resources\GradebookResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGradebooks extends ListRecords
{
    protected static string $resource = GradebookResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
