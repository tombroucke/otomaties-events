<?php

namespace Otomaties\Events\Models;

class Subscription
{
    private $subscriptionId;

    public function __construct(int $subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    public function getId() : int
    {
        return $this->subscriptionId;
    }

    public function get(string $key, bool $single = true)
    {
        return get_post_meta($this->getId(), $key, $single);
    }

    public function tickets() : array
    {
        $tickets = $this->get('tickets');
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
        return new Event($this->get('event_id'));
    }
}
