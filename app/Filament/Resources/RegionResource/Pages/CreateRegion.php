<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Resources\RegionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegion extends CreateRecord
{
    protected static string $resource = RegionResource::class;

    protected static ?string $title = 'Yeni Bölge';
}
