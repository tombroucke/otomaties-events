<?php

namespace Otomaties\Events\Models;

use Otomaties\Events\FormField;

class Event
{
    private $id = 0;

    public function __construct(int $eventId)
    {
        $this->id = $eventId;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function name() : string
    {
        return get_the_title($this->getId());
    }

    public function tickets() : array
    {
        $tickets = array_filter((array)get_field('tickets', $this->getId()));
        return array_map(function ($ticket) {
            return new Ticket($ticket);
        }, $tickets);
    }
    
    public function ticket(string $slug) {
        $tickets = $this->tickets();
        $filteredTickets = array_filter($tickets, function ($ticket) use ($slug) {
            return $slug == $ticket->slug();
        });

        if (empty($filteredTickets)) {
            return null;
        }
        return array_values($filteredTickets)[0];
    }

    public function extraFormFields() {
        $fields = array_filter((array)get_field('extra_fields', $this->getId()));
        return array_map(function ($field) {
            return new FormField($field);
        }, $fields);
    }

    public function extraFormField($slug) {
        $extraFormFields = $this->extraFormFields();
        $filteredExtraFormFields = array_filter($extraFormFields, function ($extraFormField) use ($slug) {
            return $slug == $extraFormField->slug();
        });

        if (empty($filteredExtraFormFields)) {
            return null;
        }

        return array_values($filteredExtraFormFields)[0];
    }
}
