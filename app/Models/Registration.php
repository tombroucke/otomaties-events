<?php

namespace Otomaties\Events\Models;

use Otomaties\WpModels\PostType;

class Registration extends PostType
{
    public function tickets() : array
    {
        $tickets = $this->meta()->get('tickets');
        if (!$tickets) {
            $tickets = [];
        }
        return $tickets;
    }

    public function ticketCount() : int
    {
        $count = 0;
        foreach ($this->tickets() as $name => $ticketCount) {
            $count += $ticketCount;
        }
        return $count;
    }

    public function ticketCountFor(string $ticketKey)
    {
        $count = 0;
        foreach ($this->tickets() as $name => $ticketCount) {
            if ($name == $ticketKey) {
                $count += $ticketCount;
            }
        }
        return $count;
    }

    public function event() : Event
    {
        return new Event($this->meta()->get('event_id'));
    }

    public static function postType() : string
    {
        return 'registration';
    }
}
