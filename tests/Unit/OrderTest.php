<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function convertingToAnArray()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 5);    
        
        $result = $order->toArray();m

        $this->assertEquals([
            'email' => 'jane@example.com',
            
        ]);
    }

    /** @test */
    public function creatingAnOrderFromTicketsEmailAndAmount()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email());
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    // /** @test */
    // public function creatingAnOrderFromAReservation()
    // {
    //     $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
    //     $tickets = factory(Ticket::class)->create(['concert_id' => $concert->id]);
    //     $reservation = new Reservation($tickets, 'john@example.com');

    //     $order = Order::fromReservation($reservation);

    //     $this->assertEquals('john@example.com', $order->email);
    //     $this->assertEquals(3, $order->ticketQuantity());
    //     $this->assertEquals(3600, $order->amount());
    // }

    /** not needed */
    // public function ticketsAreReleasedWhenAnOrderIsCancelled()
    // {
    //     $concert = factory(Concert::class)->create()->addTickets(10);
    //     $order = $concert->orderTickets('jane@example.com', 5);
    //     $this->assertEquals(5, $concert->ticketsRemaining());

    //     $order->cancel();

    //     $this->assertEquals(10, $concert->ticketsRemaining());
    //     $this->assertNull(Order::find($order->id));
    // }
}
