<?php

namespace NSchouten\FilamentImageManager\Observers;

use NSchouten\FilamentImageManager\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use NSchouten\FilamentImageManager\Jobs\ConvertImage;

class ImageObserver
{
    /**
     * Triggered when an Image is created.
     * Dispatches conversion jobs for each conversion configuration.
     *
     * @param Image $image The created image instance.
     */
    public function created(Image $image): void {
        foreach(config('image-manager.conversions') as $conversion) {
            ConvertImage::dispatch($image, $conversion);
        }
    }

    /**
     * Triggered when an Image is changed.
     * Dispatches conversion jobs for each conversion configuration.
     *
     * @param Image $image The created image instance.
     */
    public function updated(Image $image): void
    {
        if($image->getOriginal('path') !== $image->path || $image->getOriginal('disk') !== $image->disk) {
            foreach(config('image-manager.conversions') as $conversion) {
                ConvertImage::dispatch($image, $conversion);
            }
        }
    }

    /**
     * Triggered when an Image is permanently deleted.
     * Deletes the image file and its associated conversions from storage.
     *
     * @param Image $image The image instance being deleted.
     */
    public function forceDeleted(Image $image): void
    {
        // Deletes the main image file from the storage disk.
        Storage::disk( $image->disk )->delete( $image->path );

        // Logs the deletion of the main image file.
        Log::info('[FILAMENT IMAGE MANAGER] Image {id} (with path: {path}) deleted from disk {disk}', [
            'id' => $image->id,
            'path' => $image->path,
            'disk' => $image->disk
        ]);

        // Deletes all conversion files related to the image.
        if(is_array($image->conversions)) {
            foreach($image->conversions as $conversion => $path) {
                try {
                    Storage::disk( $image->disk )->delete( $path );
                } catch(\Exception $e) {
                    // Logs a warning if a conversion file couldn't be deleted.
                    Log::warning('[FILAMENT IMAGE MANAGER] Couldn\'t delete conversion {conversion} for image {id} (with path: {path}) on disk {disk}', [
                        'conversion' => $conversion,
                        'id' => $image->id,
                        'disk' => $image->disk,
                        'path' => $path
                    ]);
                }
            }
        }
    }
}
