<?php

namespace NSchouten\FilamentImageManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class RemoveOldImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-trashed-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all images trashed longer then 30 days ago.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $removeAfter = config('image-manager.autoRemoveAfter');
        if($removeAfter !== null && is_numeric($removeAfter)) {
            $thresholdDate = Carbon::now()->subDays($removeAfter);
            $imageClass = \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel();
            $instance = $this;
            $imageClass::onlyTrashed()
                       ->where('deleted_at', '<', $thresholdDate)
                       ->chunk(100, function (Collection $images) use ($instance) {
                            foreach ($images as $image) {
                                $instance->info('Remove: '.$image->id);
                                $image->forceDelete();
                            }
                        });
            $this->info('Removing of old images is done.');
        }
    }
}
