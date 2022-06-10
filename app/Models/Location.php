<?php

namespace Otomaties\Events\Models;

use Otomaties\WpModels\PostType;

class Location extends PostType
{
    public function information() {
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
