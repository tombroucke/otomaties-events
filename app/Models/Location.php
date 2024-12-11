<?php

namespace Otomaties\Events\Models;

use Otomaties\WpModels\PostType;
use Otomaties\AcfObjects\Fields\GoogleMap;
use Otomaties\AcfObjects\Facades\AcfObjects;
use Otomaties\AcfObjects\Contracts\FieldContract;
use Otomaties\AcfObjects\Contracts\CollectionContract;

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
     * @return FieldContract | CollectionContract | bool
     */
    public function map() : FieldContract|CollectionContract|bool
    {
        return AcfObjects::getField('map', $this->getId());
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
