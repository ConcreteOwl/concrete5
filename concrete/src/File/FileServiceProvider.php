<?php

namespace Concrete\Core\File;

use Concrete\Core\Application\Application;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/file' => '\Concrete\Core\File\Service\File',
            'helper/concrete/file' => '\Concrete\Core\File\Service\Application',
            'helper/image' => '\Concrete\Core\File\Image\BasicThumbnailer',
            'helper/mime' => '\Concrete\Core\File\Service\Mime',
            'helper/zip' => '\Concrete\Core\File\Service\Zip',
        ];

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->singleton(\Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService::class);

        $this->app->bind('image/imagick', '\Imagine\Imagick\Imagine');
        $this->app->bind('image/gd', '\Imagine\Gd\Imagine');
        $this->app->bind('image/thumbnailer', '\Concrete\Core\File\Image\BasicThumbnailer');

        $this->app->bind(StorageLocationInterface::class, function ($app) {
            return StorageLocation::getDefault();
        });

        $this->app->bindIf(Service\VolatileDirectory::class, function (Application $app) {
            return $app->build(
                Service\VolatileDirectory::class,
                [
                    'parentDirectory' => $app->make('helper/file')->getTemporaryDirectory(),
                ]
            );
        });
    }
}
