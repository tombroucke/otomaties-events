<?php

namespace Otomaties\Events\Models;

class Ticket
{
    private $ticket;

    public function __construct(array $ticket)
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

    public function ticketLimitPerRegistration()
    {
        return $this->get('ticket_limit_per_registration');
    }

    public function registrationLimit()
    {
        return $this->get('registration_limit');
    }

    public function slug() {
        return sanitize_title($this->title());
    }
}
