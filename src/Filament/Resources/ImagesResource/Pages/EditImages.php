<?php

namespace NSchouten\FilamentImageManager\Filament\Resources\ImagesResource\Pages;

use NSchouten\FilamentImageManager\Filament\Resources\ImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditImages extends EditRecord
{
    protected static string $resource = ImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['disk'] = config('image-manager.disk');
        $data['directory'] = config('image-manager.directory');

        $data['mime_type'] = Storage::disk( config('image-manager.disk') )->mimeType($data['path']);
        $data['size'] = Storage::disk( config('image-manager.disk') )->size($data['path']);
 
        return $data;
    }
}
