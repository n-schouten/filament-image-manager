<?php

namespace NSchouten\FilamentImageManager\Livewire;

// Import necessary classes for this component.
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Filament\Notifications\Notification;
use NSchouten\FilamentImageManager\Traits\HasStorageOptions;
use NSchouten\FilamentImageManager\Jobs\ConvertImage;

// Define the Livewire component class for handling image uploads.
class UploadImage extends Component
{
    // Use the WithFileUploads trait to enable file upload functionality.
    use WithFileUploads, HasStorageOptions;
 
    // Declare the public variables used by the component.
    public string $field;

    // Apply validation rules for the 'image' property, ensuring the file is required, is an image, and does not exceed 2MB in size.
    #[Validate('required|image|max:2048')]
    public $image;

    // Render the view for this Livewire component.
    public function render() {
        // Return the view for the upload-image component, defined in 'filament-image-manager::livewire.upload-image'.
        return view('filament-image-manager::livewire.upload-image');
    }

    // Handle the final upload logic after the image has been validated.
    public function finalizeUpload() {
        // Validate the input data using the validation rules defined above.
        $this->validate();

        // Retrieve the image model class from the ImagesResource.
        $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();

        // Check if the authenticated user has permission to create an image (this checks user roles/permissions).
        if (auth()->user()->cannot('create', $imageClass)) {
            // If the user does not have permission, abort with a 403 Forbidden response.
            abort(403);
        }

        // Store the uploaded image in the specified directory and disk, and retrieve the file path.
        $path = $this->image->store($this->directory, $this->disk);

        // Retrieve additional information about the image.
        $mime = $this->image->getMimeType();  // The MIME type of the image (e.g., image/jpeg).
        $name = $this->image->getClientOriginalName();  // The original file name.
        $size = $this->image->getSize();  // The size of the uploaded file in bytes.

        // Save the image information in the database using the image model.
        $save = $imageClass::create([
            'disk' => $this->disk,
            'directory' => $this->directory,
            'name' => $name,
            'path' => $path,
            'size' => $size,
            'mime' => $mime
        ]);

        // Reset the image property to null after upload.
        $this->image = null;
        
        // Dispatch events to close the modal and select the newly uploaded image.
        $this->dispatch('close-modal', id: 'filament-upload-image');
        $this->dispatch('select-image', field: $this->field, url: $save->url, id: $save->id);

        // Send a success notification to the user that the upload was successful.
        Notification::make()
            ->title('Uploaded successfully')  // Notification title.
            ->success()  // Set the notification type to success.
            ->send();  // Send the notification.
    }

    // Check if the uploaded image is previewable (only previewable if it is an image).
    public function isPreviewable(): bool {
        // Ensure the file is an image by checking the MIME type.
        return $this->image && str_starts_with($this->image->getMimeType(), 'image/');
    }
}
