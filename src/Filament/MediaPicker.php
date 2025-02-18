<?php

namespace NSchouten\FilamentImageManager\Filament;

use Filament\Forms\Components\Field;
use Illuminate\Validation\Rules\Exists;

class MediaPicker extends Field
{
    // The view that will be used for rendering the field.
    protected string $view = 'filament-image-manager::filament.media-picker';

    // Storage disk configuration for the media picker.
    protected ?string $disk = null;
    
    // Directory configuration for the media picker.
    protected ?string $directory = null;
    
    // Flag to enforce the disk setting.
    protected ?bool   $diskForced = false;
    
    // Flag to enforce the directory setting.
    protected ?bool   $directoryForced = false;

    /**
     * Create a new MediaPicker field.
     * 
     * This method initializes the field and sets up the validation rules to ensure that the selected media exists in the database.
     * 
     * @param string $name The name of the field.
     * @return static The instance of the MediaPicker field.
     */
    public static function make(string $name): static
    {
        // Get the image model associated with the image manager.
        $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();
        
        // Call the parent make method and add validation to check if the image exists.
        return parent::make($name)
            ->exists(column: 'id', table: $imageClass);
    }

    /**
     * Set the storage disk.
     *
     * @param string $disk The disk to be used for storage.
     * @param bool $forceUse Whether to enforce this disk in the validation rule.
     * @return static The current instance of MediaPicker.
     */
    public function disk(string $disk, bool $forceUse = false): static
    {
        // Set the disk and enforce flag.
        $this->disk = $disk;
        $this->diskForced = $forceUse;
        
        // If forced, apply the force-use validation rule, otherwise return the instance.
        return $forceUse ? $this->applyForceUse() : $this;
    }

    /**
     * Get the encrypted disk value.
     *
     * @return string The disk value, either from the object or the default configuration.
     */
    public function getDisk(): string
    {
        return $this->disk ?? config('image-manager.disk');
    }

    /**
     * Get the disk forced flag.
     *
     * @return bool Whether the disk is forced or not.
     */
    public function getDiskForced(): bool {
        return $this->diskForced ?? config('image-manager.diskForced');
    }

    /**
     * Set the storage directory.
     *
     * @param string $directory The directory to be used for storage.
     * @param bool $forceUse Whether to enforce this directory in the validation rule.
     * @return static The current instance of MediaPicker.
     */
    public function directory(string $directory, bool $forceUse = false): static
    {
        // Set the directory and enforce flag.
        $this->directory = $directory;
        $this->directoryForced = $forceUse;
        
        // If forced, apply the force-use validation rule, otherwise return the instance.
        return $forceUse ? $this->applyForceUse() : $this;
    }

    /**
     * Get the directory forced flag.
     *
     * @return bool Whether the directory is forced or not.
     */
    public function getDirectoryForced(): bool {
        return $this->diskForced ?? config('image-manager.diskForced');
    }

    /**
     * Get the encrypted directory value.
     *
     * @return string The directory value, either from the object or the default configuration.
     */
    public function getDirectory(): string
    {
        return $this->directory ?? config('image-manager.directory');
    }

    /**
     * Apply the force-use settings for disk and directory.
     *
     * This method applies the validation rule dynamically by modifying it to check the disk and directory settings if they are forced.
     *
     * @return static The current instance of MediaPicker.
     */
    protected function applyForceUse(): static
    {
        // Retrieve disk, directory, and forced flags.
        $disk = $this->disk;
        $directory = $this->directory;

        $isDiskForced = $this->diskForced;
        $isDirectoryForced = $this->directoryForced;
        
        // Get the image model associated with the image manager.
        $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();

        // Modify the validation rule dynamically based on the disk and directory forced settings.
        return $this->exists(
            column: 'id',
            table: $imageClass,
            modifyRuleUsing: function (Exists $rule) use ($disk, $directory, $isDiskForced, $isDirectoryForced) {
                // Dynamically modify the validation rule based on force-use settings
                if ($isDiskForced) {
                    $rule = $rule->where('disk', $disk);
                }

                if ($isDirectoryForced) {
                    $rule = $rule->where('directory', $directory);
                }

                return $rule;
            }
        );
    }

    /**
     * Get the URL of an image by its ID.
     *
     * @param int $id The ID of the image.
     * @return string|null The URL of the image or null if not found.
     */
    public function getImage($id): ?string {
        // Get the image model associated with the image manager.
        $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();
        
        // Find the image by ID and return its URL if available.
        return $imageClass::find($id)->url ?? null;
    }
}
