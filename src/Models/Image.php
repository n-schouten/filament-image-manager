<?php

namespace NSchouten\FilamentImageManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use NSchouten\FilamentImageManager\Observers\ImageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use NSchouten\FilamentImageManager\Interfaces\ImageModel;

// This model is observed by the ImageObserver
#[ObservedBy([ImageObserver::class])]
class Image extends Model
{
    // Using SoftDeletes for soft delete functionality (mark record as deleted without actually removing it from the database)
    use SoftDeletes;

    // The attributes that are mass assignable (can be set via the constructor or fill method)
    protected $fillable = [
        'disk',
        'directory',
        'name',
        'path',
        'size',
        'mime',
        'alt',
        'title',
        'copyright',
        'conversions',
    ];

    // Cast certain attributes to specific types (array for 'copyright' and 'conversions')
    protected $casts = [
        'copyright' => 'array',
        'conversions' => 'array',
    ];

    // A helper function to generate a URL for an image file, depending on its disk storage
    protected function generateUrl(string $path): string {
        // Check if the disk is not 'public' and generate a temporary URL
        if ($this->disk !== 'public') {
            return Storage::disk($this->disk)->temporaryUrl($path, now()->addMinutes(60)); // Temporary URL valid for 60 minutes
        }
        // Otherwise, generate a permanent URL for public disk
        return Storage::disk($this->disk)->url($path);
    }

    // Accessor to get the image URL using the 'path' attribute
    public function getUrlAttribute(): string {
        return $this->generateUrl($this->path);
    }

    // A method to retrieve a specific conversion of the image (if available)
    public function getConversion(string $conversion): string {
        // If conversions are defined, return the URL for the specific conversion
        if (!empty($this->conversions) && array_key_exists($conversion, $this->conversions)) {
            return $this->generateUrl($this->conversions[$conversion]);
        }
        // Otherwise, return the default URL
        return $this->url;
    }

    // A getter method to access the disk the image is stored on
    public function getDisk(): string {
        return $this->disk;
    }
}
