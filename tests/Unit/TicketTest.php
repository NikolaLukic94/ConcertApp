<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function aTicketCanBeReleased()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(1);
        $order = $concert->orderTicket('jane@example.com');
        $ticket = $order->tickets()->first();
        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();
        // fresh will do a fresh query of DB
        $this->assertNull($ticket->fresh()->order_id);
    }
}
