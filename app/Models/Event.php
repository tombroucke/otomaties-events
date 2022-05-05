<?php

namespace Otomaties\Events\Models;

use DateTime;
use Otomaties\Events\FormField;
use Otomaties\WpModels\PostType;

class Event extends PostType
{

    public function __construct($id)
    {
        if (strpos($id, 'event_') !== false) {
            $id = str_replace('event_', '', $id);
        }
        parent::__construct($id);
    }
    
    public function date() : DateTime
    {
        $date = $this->meta()->get('date');
        return $date ? DateTime::createFromFormat('Ymd', $date) : null;
    }

    public function time()
    {
        return substr($this->meta()->get('time'), 0, 5);
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

    public function registrations()
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

    public function registrationsOpen()
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

    public function freeSpots()
    {
        $availableSpots = $this->meta()->get('registration_limit');
        if (!$availableSpots) {
            return 99999;
        }
        
        return $availableSpots - $this->soldTickets();
    }

    public static function postType() : string
    {
        return 'event';
    }
}
