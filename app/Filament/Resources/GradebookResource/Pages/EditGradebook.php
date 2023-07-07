<?php

namespace App\Filament\Resources\GradebookResource\Pages;

use App\Filament\Resources\GradebookResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGradebook extends EditRecord
{
    protected static string $resource = GradebookResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
