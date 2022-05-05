<?php

namespace Otomaties\Events\Models;

class TicketType
{
    public function __construct(private array $ticket, private Event $event)
    {
        $defaultTicket = [
            'title' => __('Personal registration', 'otomaties-events'),
            'ticket_limit_per_registration' => -1,
            'registration_limit' => -1,
        ];
        $this->ticket = wp_parse_args($ticket, $defaultTicket);
    }

    public function get($key)
    {
        return $this->ticket[$key] ?? null;
    }

    public function title()
    {
        return $this->get('title');
    }

    public function ticketLimitPerRegistration() : int
    {
        return $this->get('ticket_limit_per_registration') ?: $this->registrationLimit();
    }

    public function registrationLimit() : int
    {
        return $this->get('registration_limit') ?: 9999999;
    }

    public function slug()
    {
        return sanitize_title($this->title());
    }

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

    public function availableTickets()
    {
        return $this->registrationLimit() - $this->soldTickets();
    }

    public function isAvailable()
    {
        return $this->availableTickets() > 0;
    }
}
