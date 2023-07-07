<?php

namespace App\Filament\Resources\MyGradebookResource\Pages;

use App\Filament\Resources\MyGradebookResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyGradebook extends EditRecord
{
    protected static string $resource = MyGradebookResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
