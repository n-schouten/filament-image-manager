<?php

namespace NSchouten\FilamentImageManager\Support\Conversions;

use NSchouten\FilamentImageManager\Models\Image;
use Spatie\Image\Image as ImageConverter;
use Spatie\Image\Enums\ImageDriver;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Facades\Storage;

class Conversions
{
    /**
     * Create a square cropped conversion.
     *
     * @return void
     */
    public static function square(ImageConverter $image): ImageConverter
    {
        return $image->fit(Fit::Crop, 250, 250)
                     ->optimize();
    }

    /**
     * Create a header cropped conversion.
     *
     * @return void
     */
    public static function header(ImageConverter $image): ImageConverter
    {
        return $image->fit(Fit::Crop, 1920, 600)
                     ->optimize();
    }
}
