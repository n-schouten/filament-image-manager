<?php

namespace NSchouten\FilamentImageManager\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use NSchouten\FilamentImageManager\Traits\HasStorageOptions;

class ImageSelector extends Component
{
    use WithPagination, WithoutUrlPagination, HasStorageOptions;

    public string $field;
    public int|string $perPage = 10;

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('filament-image-manager::livewire.image-selector', [
            'images' => $this->fetchImages(),
        ]);
    }

    /**
     * Fetch images based on disk and directory conditions.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchImages()
    {
        $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();
        $query = $imageClass::query();

        // Apply disk filter if forced
        if ($this->diskForced) {
            $query->where('disk', $this->disk);
        }

        // Apply directory filter if forced
        if ($this->directoryForced) {
            $query->where('directory', $this->directory);
        }

        // Return paginated result
        return $query->paginate($this->perPage);
    }

    /**
     * Handle image selection.
     *
     * @param int $id
     * @param string $preview
     * @return void
     */
    public function selectImage(int $id, string $preview): void
    {
        $this->dispatch('select-image', [
            'field' => $this->field,
            'url' => $preview,
            'id' => $id
        ]);

        $this->dispatch('close-modal', ['id' => 'filament-select-image']);
    }
}
