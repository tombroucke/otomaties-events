<?php

namespace Otomaties\Events\Models;

use Otomaties\Events\Formatter;

class TicketType
{
    /**
     * Set up ticket type
     *
     * @param array $ticket
     * @param Event $event
     */
    public function __construct(private array $ticket, private Event $event)
    {
        $defaultTicket = [
            'title' => __('Personal registration', 'otomaties-events'),
            'price' => 0,
            'ticket_limit_per_registration' => -1,
            'registration_limit' => -1,
        ];
        $this->ticket = wp_parse_args($ticket, $defaultTicket);
    }

    /**
     * Get ticket meta
     *
     * @param string $key
     * @return string|integer
     */
    public function get(string $key) : string|int
    {
        return $this->ticket[$key] ?? null;
    }

    /**
     * Get ticket title
     *
     * @return string
     */
    public function title() : string
    {
        return $this->get('title');
    }

    /**
     * Ticket price
     *
     * @return integer|null
     */
    public function price() : ?int
    {
        $price = $this->get('price');
        return $price !== '' ? (int)$price : null;
    }

    /**
     * Get formatted price
     *
     * @param string $prepend
     * @param string $append
     * @return string|null
     */
    public function priceHtml(string $prepend = '', string $append = '') : ?string
    {
        $price = $this->price();
        if ($price === null) {
            return '';
        }

        $return = $prepend;
        if (0 ==! $price) {
            $return .= Formatter::currency($price);
        } else {
            $return .= __('Free', 'otomaties-events');
        }
        $return .= $append;
        return $return;
    }
    
    /**
     * Ticket limit per registration
     *
     * @return integer
     */
    public function ticketLimitPerRegistration() : int
    {
        return $this->get('ticket_limit_per_registration') ?: $this->registrationLimit();
    }

    /**
     * Get registration limit
     *
     * @return integer
     */
    public function registrationLimit() : int
    {
        return $this->get('registration_limit') ?: 9999999;
    }

    /**
     * Get ticket slug
     *
     * @return string
     */
    public function slug() : string
    {
        return sanitize_title($this->title());
    }

    /**
     * Get number of sold tickets
     *
     * @return integer
     */
    public function soldTickets() : int
    {
        $count = 0;
        $registrations = $this->event->registrations();
        foreach ($registrations as $registration) {
            foreach ($registration->tickets() as $ticketslug => $ticketCount) {
                if ($ticketslug == $this->slug()) {
                    $count += $ticketCount;
                }
            }
        }
        return $count;
    }

    /**
     * Get available ticket count
     * Gets the lowest ticket count from global event or this ticket
     *
     * @return int
     */
    public function availableTickets() : int
    {
        $eventFreeSpots = $this->event->freeSpots();
        $ticketLimit = $this->registrationLimit() - $this->soldTickets();
        return min($eventFreeSpots, $ticketLimit);
    }

    /**
     * Test if ticket is available
     *
     * @return boolean
     */
    public function isAvailable() : bool
    {
        return $this->availableTickets() > 0;
    }
}
