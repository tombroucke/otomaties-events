<?php

namespace Otomaties\Events\Models;

use Otomaties\WpModels\PostType;

class Location extends PostType
{
    /**
     * Get location information
     *
     * @return string
     */
    public function information() : string
    {
        return $this->meta()->get('location_information');
    }

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
