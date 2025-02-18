<?php

namespace NSchouten\FilamentImageManager\Support\Conversions;

use Spatie\Image\Image as ImageConverter;
use Spatie\Image\Enums\ImageDriver;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Facades\Storage;
use NSchouten\FilamentImageManager\Models\Image;

class Convert 
{
    protected $image; // Holds the Image model instance.
    protected string $tempPath; // Path to the temporary file used during processing.

    /**
     * Create a new instance of the converter with a temporary file.
     *
     * @param $image The Image model instance to be processed.
     * @return static Returns the instance of the Convert class.
     * 
     * This method copies the image from the specified disk to a temporary local path for processing. 
     * Throws an exception if the image does not exist on the disk.
     */
    public static function make(Image $image): static
    {
        $instance = new static();
        $instance->image = $image;
        $instance->tempPath = '/filament-tmp/' . $image->path;

        if(Storage::disk($image->disk)->exists($image->path)) {
            $fileContent = Storage::disk($image->disk)->get($image->path);
            Storage::disk('local')->put($instance->tempPath, $fileContent);
        } else {
            throw new \Exception('Image: ' . $image->id . ' doesn\'t exists on disk '.$image->disk);
        }

        return $instance;
    }

    /**
     * Load the temporary image for processing.
     *
     * @return \Spatie\Image\Image The image loaded using the Spatie Image package.
     * 
     * This method initializes the image processor using the GD driver and loads the temporary file.
     */
    protected function loadImage(): \Spatie\Image\Image
    {
        return ImageConverter::useImageDriver(ImageDriver::Gd)
                             ->loadFile( Storage::disk('local')->path($this->tempPath) );
    }

    /**
     * Update the image model with the new conversion path.
     *
     * @param string $path The path to the new converted image.
     * @param string $conversion The name of the conversion performed.
     * @return void
     * 
     * Updates the `conversions` field in the Image model with the new path for the specified conversion.
     */
    protected function updateModel(string $path, string $conversion): void
    {
        $conversions = $this->image->conversions;
        $conversions[$conversion] = $path;
        $this->image->conversions = $conversions;
        $this->image->save();
    }

    /**
     * Save the processed image to the storage disk.
     *
     * @param string $newPath The path where the processed image will be saved.
     * @return void
     * 
     * Reads the processed image from the temporary local storage and saves it to the original storage disk.
     */
    protected function saveImage(string $newPath): void
    {
        $processedImage = Storage::disk('local')->get($this->tempPath);
        Storage::disk($this->image->disk)->put($newPath, $processedImage);
    }

    /**
     * Delete the temporary file.
     *
     * @return void
     * 
     * Cleans up by deleting the temporary file after the conversion process is complete.
     */
    protected function deleteTempFile(): void
    {
        Storage::disk('local')->delete($this->tempPath);
    }

    /**
     * Convert the file
     * 
     * @param string $conversion The name of the conversion method to be applied.
     * @return bool Returns true if the conversion is successful, false otherwise.
     * 
     * This method handles the entire conversion process:
     * 1. Checks if the specified conversion method exists.
     * 2. Applies the conversion using the custom `conversionsClass`.
     * 3. Saves the new image to the appropriate path.
     * 4. Updates the model with the new conversion path.
     * 5. Cleans up temporary files.
     */
    public function convert(string $conversion): bool 
    {
        $filename = pathinfo($this->image->path, PATHINFO_FILENAME);
        $newPath = $this->image->directory . '/conversions/' . $conversion . '/' . $filename . config('image-manager.fileType', '.webp');
        
        // Check if the specified conversion method exists in the configured conversions class.
        if (method_exists(config('image-manager.conversionsClass'), $conversion)) {
            // Perform the conversion and save the processed image.
            $image = config('image-manager.conversionsClass')::$conversion($this->loadImage())->save();
            $this->saveImage($newPath);
            
            // Clean up temporary files and update the model with the new path.
            $this->deleteTempFile();
            $this->updateModel($newPath, $conversion);
            return true;
        }
        
        // Return false if the specified conversion method does not exist.
        return false;
    }
}
