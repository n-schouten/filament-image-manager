<?php
 
namespace NSchouten\FilamentImageManager;
 
use DanHarrin\FilamentBlog\Pages\Settings;
use DanHarrin\FilamentBlog\Resources\CategoryResource;
use DanHarrin\FilamentBlog\Resources\PostResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
 
class ImageManagerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'image-manager';
    }
 
    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::class
            ])
            ->pages([]);
    }
 
    public function boot(Panel $panel): void
    {}
}
