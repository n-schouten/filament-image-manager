<?php

namespace NSchouten\FilamentImageManager\Filament\Resources\ImagesResource\Pages;

use NSchouten\FilamentImageManager\Filament\Resources\ImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateImages extends CreateRecord
{
    protected static string $resource = ImagesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['disk'] = config('image-manager.disk');
        $data['directory'] = config('image-manager.directory');

        $data['mime'] = Storage::disk( config('image-manager.disk') )->mimeType($data['path']);
        $data['size'] = Storage::disk( config('image-manager.disk') )->size($data['path']);
 
        return $data;
    }
}
