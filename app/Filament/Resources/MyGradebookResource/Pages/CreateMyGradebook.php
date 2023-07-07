<?php

namespace App\Filament\Resources\MyGradebookResource\Pages;

use App\Filament\Resources\MyGradebookResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyGradebook extends CreateRecord
{
    protected static string $resource = MyGradebookResource::class;
}
