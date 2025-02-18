<?php
return [
    // Specifies the storage disk where files will be saved (e.g., 'public', 's3', etc.)
    'disk'              => 'public',

    // Determines whether to only load images from the selected disk.
    'diskForced'        => true,

    // Defines the directory structure where the files will be stored
    'directory'         => 'images',

    // Determines whether to only load images from the selected directory.
    'directoryForced'   => false,

    // Specifies the class that will handle image conversions. This class is responsible for processing images into different formats or sizes.
    'conversionsClass'  => NSchouten\FilamentImageManager\Support\Conversions\Conversions::class,

    // Lists the available image conversions that can be applied (e.g., 'square', 'header'). These could be different size formats or orientations.
    'conversions'       => ['square','header'],

    // Points to the policy class that defines access control or permissions related to image actions.
    'policy'            => NSchouten\FilamentImageManager\Policies\ImagePolicy::class,

    // Defines the default file type for the images. In this case, images will be saved as '.webp'.
    'fileType'          => '.webp',

    // Specifies the model that represents the image data. It is likely used to interact with the database.
    'model'             => NSchouten\FilamentImageManager\Models\Image::class,

    // Sets the number of days after which images will be automatically removed (here, 30 days).
    'autoRemoveAfter'   => 30
];
