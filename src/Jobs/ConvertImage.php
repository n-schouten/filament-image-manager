<?php

namespace NSchouten\FilamentImageManager\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;
use NSchouten\FilamentImageManager\Support\Conversions\Convert;
use NSchouten\FilamentImageManager\Models\Image;

class ConvertImage implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        public Image $image,
        public string $conversion
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Convert::make($this->image)->convert($this->conversion);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return md5($this->image->id . '_' . $this->conversion);
    }
}
