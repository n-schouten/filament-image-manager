<?php
namespace NSchouten\FilamentImageManager\Traits;

use Livewire\Attributes\Locked;

trait HasStorageOptions {
    #[Locked]
    public string $disk;

    #[Locked]
    public string $directory;

    #[Locked]
    public bool $diskForced;

    #[Locked]
    public bool $directoryForced;
}
