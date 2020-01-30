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
        
        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function ticketsAreReleasedWhenAnOrderIsCancelled()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);
        $order = $concert->orderTickets('jane@example.com', 5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
}
