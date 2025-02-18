<?php

namespace NSchouten\FilamentImageManager;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;
use NSchouten\FilamentImageManager\Models\Image;
use Illuminate\Support\Facades\Schedule;

class ImageManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register() {}

    /**
     * Bootstrap any package services.
     */
    public function boot() {
        // Register console commands for the package
        if ($this->app->runningInConsole()) {
            $this->commands([
                \NSchouten\FilamentImageManager\Console\Commands\RefreshConversions::class,
                \NSchouten\FilamentImageManager\Console\Commands\RemoveOldImages::class
            ]);
        }
        
        // Load views from the package's "resources/views" directory
        // Assign them a namespace ("filament-image-manager") to be used in blade templates
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-image-manager');

        // Publish migrations to the host application's "database/migrations" directory
        // This allows the main application to run or modify the migrations
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations')
        ], 'image-manager-migrations');

        // Publish the package's configuration file to the main application's "config" directory
        // This enables the application to customize the package's configuration
        $this->publishes([
            __DIR__.'/../config/filament-image-manager.php' => config_path('image-manager.php'),
        ], 'image-manager-config');

        // Publish localization files to the application's "lang/vendor" directory
        // This allows for translating package strings to other languages
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/filament-image-manager'),
        ], 'image-manager-locales');

        // Merge the default package configuration with the application's configuration
        // This ensures the package has default values while allowing overrides
        $this->mergeConfigFrom(__DIR__.'/../config/filament-image-manager.php', 'image-manager');

        // Load translations directly from the package's "lang" directory
        // Assign the namespace "image-manager" for easy reference in translation functions
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'image-manager');

        // Register Livewire components for use in the application
        // These components can be used in blade templates for specific image manager functionality
        Livewire::component('image-manager-upload-image', \NSchouten\FilamentImageManager\Livewire\UploadImage::class);
        Livewire::component('image-manager-image-selector', \NSchouten\FilamentImageManager\Livewire\ImageSelector::class);

        $this->registerGate();
        $this->registerCommands();
    }

    /**
     * Register the commands
     * 
     */
    protected function registerCommands():void {
        $this->app->booted(function () {
            Schedule::command('app:remove-trashed-image')->daily();
        });
    }

    /**
     * Register the gate
     */
    protected function registerGate():void {
        if(config('image-manager.policy') !== null) {
            Gate::policy(
                \NSchouten\FilamentImageManager\Filament\Resources\ImagesResource::getModel(),
                config('image-manager.policy')
            );
        }
    }
}
