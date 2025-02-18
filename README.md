
# Filament Image Manager

A simple yet powerful image manager for Laravel Filament

## Features

- Field for Filament to select or upload an image
- Working with different disks/directories
- Filament Resource for overview of all images
- Performing conversions with ```Spatie/Image```
- Soft Deletes of Images, cleaning up the database record and belonging files after a set amount of days.

## Installation

You can install this package with Composer. Make sure Laravel & Filament are installed in your main project already.

```bash
  composer install n-schouten/filament-image-manager
```

After installation you need to publish the migrations with the following command:
```bash
  php artisan vendor:publish --tag=image-manager-migrations
```

You also may publish the configuration file, this is however not necessary:
```bash
  php artisan vendor:publish --image-manager-config
```
