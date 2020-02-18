<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class)
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    // public function cancel()
    // {
    //     foreach ($this->tickets as $ticket) {
    //         $ticket->release();
    //     }
        
    //     $this->delete();
    // }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->ticketQuantity() * $this->concert->ticket_price,
        ]
    }

    public static function forTickets($tickets, $email, $amount)
    {
        $order = self::create([
            'email' => $email,
            'amount' => $amount
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    } 

    public static function fromReservation($reservation)
    {
        $order = self::create([
            'email' => $reservation->email,
            'amount' => $reservation->totalCost()
        ]);

        $order->tickets()->saveMany($reservation->tickets());

        return $order;
    }
}

