<?php

namespace Otomaties\Events\Models;

use Otomaties\WpModels\PostType;

class Registration extends PostType
{
    /**
     * Get tickets in registration
     *
     * @return array<string, int>
     */
    public function tickets() : array
    {
        $tickets = $this->meta()->get('tickets');
        if (!$tickets) {
            $tickets = [];
        }
        return $tickets;
    }

    /**
     * Get total tickets in registration
     *
     * @return integer
     */
    public function ticketCount() : int
    {
        $count = 0;
        foreach ($this->tickets() as $name => $ticketCount) {
            $count += $ticketCount;
        }
        return $count;
    }

    /**
     * Get ticket count for specific ticket
     *
     * @param string $ticketKey
     * @return integer
     */
    public function ticketCountFor(string $ticketKey) : int
    {
        $count = 0;
        foreach ($this->tickets() as $name => $ticketCount) {
            if ($name == $ticketKey) {
                $count += $ticketCount;
            }
        }
        return $count;
    }

    /**
     * Get linked event
     *
     * @return Event|null
     */
    public function event() : ?Event
    {
        $eventId = $this->meta()->get('event_id');
        return $eventId && get_post_status($eventId) ? new Event($eventId) : null;
    }

    /**
     * Get post type
     *
     * @return string
     */
    public static function postType() : string
    {
        return 'registration';
    }
}
