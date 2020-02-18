<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute() 
    {
        return $this->date->format('F j, Y');
    }
    
    public function getFormattedStartTimeAttribute() 
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollars() 
    {
        return number_format($this->ticket_price / 100, 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function reserveTickets($quantity, $email)
    {
        $tickets = $this->findTickets($quantity)->each(function ($ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    public function hasOrderFor($customerEmail)
    {
        return $concert->orders()->where('email', $customerEmail)->count() > 0;
    }

    public function ordersFor($customerEmail)
    {
        return $concert->orders()->where('email', $customerEmail)->get();
    }

    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->tickets()->whereNull('order_id')->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughtTicketsException;
        }

        $concert->orders()->create(['email' => $email]);

        foreach($tickets as $ticket) {
            $order->tickets()->create([$ticket]);
        }

        return $order;
    }

    public function addTickets($quantity)
    {
        foreach(range(1, $ticketQuantity) as $i) {
            $order->tickets()->create([]);
        } 

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->whereNull('order_id')->count();
    }

}
