<?php

namespace Otomaties\Events\Models;

use Otomaties\WpModels\PostType;

class Location extends PostType
{
    /**
     * Get post type
     *
     * @return string
     */
    public static function postType() : string
    {
        return 'location';
    }
}