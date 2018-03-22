<?php

namespace MadeHQ\Cloudinary\Model;

use MadeHQ\Cloudinary\Model\Image;

class ImageLink extends FileLink
{
    private static $db = [
        'Gravity' => 'Varchar(15)',
        'Alt' => 'Varchar(200)',
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'File' => Image::class,
    ];

    private static $table_name = 'CloudinaryImageLink';
}
