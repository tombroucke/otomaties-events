<?php

namespace Otomaties\Events\Models;

use Otomaties\AcfObjects\Acf;
use Otomaties\WpModels\PostType;
use Otomaties\AcfObjects\GoogleMap;

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
     * Get location map
     *
     * @return GoogleMap
     */
    public function map() : GoogleMap
    {
        return Acf::getField('map', $this->getId());
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
