<?php

namespace Otomaties\Events\Models;

use DateTime;
use Otomaties\Events\FormField;
use Otomaties\WpModels\PostType;
use Otomaties\Events\Models\Location;
use Otomaties\WpModels\PostTypeCollection;

class Event extends PostType
{

    public function __construct($id)
    {
        if (strpos($id, 'event_') !== false) {
            $id = str_replace('event_', '', $id);
        }
        parent::__construct($id);
    }
    
    /**
     * Get DateTime for date or date_to
     *
     * @param string $which 'from' or 'to'
     * @return DateTime|null
     */
    public function eventDate(string $which = 'from') : ?DateTime
    {
        $dateKey = $which == 'from' ? 'date' : 'date_to';
        $timeKey = $which == 'from' ? 'time' : 'time_to';
        $dateTime = null;
        $date = $this->meta()->get($dateKey);
        if ($date) {
            $dateTime = DateTime::createFromFormat('Ymd', $date);
            $time = $this->meta()->get($timeKey);
            if ($time) {
                $timeArray = explode(':', $time);
                $dateTime->setTime($timeArray[0], $timeArray[1]);
            } else {
                $dateTime->setTime('00', '00');
            }
        }
        return $dateTime;
    }

    public function formattedDate(bool $showTime = false, string $dateFormat = null, string $timeFormat = null)
    {
        $dateParts = [
            $this->eventDate('from'),
            $this->eventDate('to')
        ];

        $dateParts = array_filter($dateParts);
        $dateParts = array_map(function ($datePart) use ($showTime, $dateFormat, $timeFormat) {
            $format = $dateFormat ?: get_option('date_format');
            if ($showTime && ($datePart->format('H') != '00' || $datePart->format('i') != '00')) {
                $format .= ' ' . ($timeFormat ?: get_option('time_format'));
            }
            return $datePart->format($format);
        }, $dateParts);
        return implode(' - ', $dateParts);
    }

    public function eventTime() : ?string
    {
        return $this->meta()->get('time') ? substr($this->meta()->get('time'), 0, 5) : null;
    }

    public function ticketTypes() : array
    {
        $ticketTypes = array_filter((array)get_field('ticket_types', $this->getId()));
        return array_map(function ($ticketType) {
            return new TicketType($ticketType, $this);
        }, $ticketTypes);
    }
    
    public function ticketType(string $slug)
    {
        $ticketTypes = $this->ticketTypes();
        $filteredTicketTypes = array_filter($ticketTypes, function ($ticketType) use ($slug) {
            return $slug == $ticketType->slug();
        });

        if (empty($filteredTicketTypes)) {
            return null;
        }
        return array_values($filteredTicketTypes)[0];
    }

    public function soldTickets() : int
    {
        $ticketCount = 0;
        foreach ($this->ticketTypes() as $ticketType) {
            $ticketCount += $ticketType->soldTickets();
        }
        return $ticketCount;
    }

    public function extraFormFields()
    {
        $fields = array_filter((array)get_field('extra_fields', $this->getId()));
        return array_map(function ($field) {
            return new FormField($field);
        }, $fields);
    }

    public function extraFormField($slug)
    {
        $extraFormFields = $this->extraFormFields();
        $filteredExtraFormFields = array_filter($extraFormFields, function ($extraFormField) use ($slug) {
            return $slug == $extraFormField->slug();
        });

        if (empty($filteredExtraFormFields)) {
            return null;
        }

        return array_values($filteredExtraFormFields)[0];
    }

    /**
     * Get all registrations for this event
     *
     * @return PostTypeCollection
     */
    public function registrations() : PostTypeCollection
    {
        return Registration::find([
            'meta_query' => [
                [
                    'key' => 'event_id',
                    'value' => $this->getId()
                ]
            ]
        ]);
    }

    /**
     * Check if registrations are open
     *
     * @return boolean
     */
    public function registrationsOpen() : bool
    {
        $availableFrom = $this->meta()->get('registration_start') ? DateTime::createFromFormat('Y-m-d H:i:s', $this->meta()->get('registration_start'), wp_timezone()) : null;
        $availableUntill = $this->meta()->get('registration_end') ? DateTime::createFromFormat('Y-m-d H:i:s', $this->meta()->get('registration_end'), wp_timezone()) : null;
        $now = new DateTime();
        $now->setTimezone(wp_timezone());

        // Not open if there are not ticket typs
        if (empty($this->ticketTypes())) {
            return false;
        }

        // Open if no deadlines are filled in
        if (!$availableFrom && !$availableUntill) {
            return true;
        }

        // If only available from is filled in
        if (!$availableUntill) {
            return $now >= $availableFrom;
        }

        // If only available untill is filled in
        if (!$availableFrom) {
            return $now <= $availableUntill;
        }

        // If both are filled in
        if ($availableFrom && $availableUntill) {
            return $now >= $availableFrom && $now <= $availableUntill;
        }

        return false;
    }

    /**
     * Get registration limit
     *
     * @return integer
     */
    public function registrationLimit() : int
    {
        return $this->meta()->get('registration_limit') ?: 99999;
    }

    /**
     * Get amount of free spots
     *
     * @return int
     */
    public function freeSpots() : int
    {
        return $this->registrationLimit() - $this->soldTickets();
    }

    /**
     * Get post type
     *
     * @return string
     */
    public static function postType() : string
    {
        return 'event';
    }

    public function location() : ?Location
    {
        $locationId = $this->meta()->get('location') ?: 0;
        $location = Location::find($locationId);
        return $location->first();
    }
}
