<?php

namespace App;

class Reservation
{
    private $tickets;
    private $email;

    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }

    public function tickets() 
    {
        return $this->tickets();
    }

    public function email()
    {
        return $this->email();
    }

    public function complete()
    {
        return Order::forTickets($this->tickets(), $this->email(), $this->totalCost());
    }
}