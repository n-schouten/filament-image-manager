<?php

namespace NSchouten\FilamentImageManager\Console\Commands;

use Illuminate\Console\Command;
use NSchouten\FilamentImageManager\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use NSchouten\FilamentImageManager\Jobs\ConvertImage;

class RefreshConversions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-conversions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the image conversions of the app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $artisan = $this;
        $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();
        $imageClass::chunk(100, function (Collection $images) use($artisan) {
            foreach ($images as $image) {
                foreach( config('image-manager.conversions') as $conversion ) {
                    if(ConvertImage::dispatch($image, $conversion)) {
                        $artisan->info('Image: ' . $image->id . ' succesfully dispatched to jobs');
                    }
                }
            }
        });
    }
}
