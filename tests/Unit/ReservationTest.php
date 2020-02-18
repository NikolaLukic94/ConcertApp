<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function calculationTheTotalCost()
    {
        $ticket = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    public function retrievingTheCustomersEmail()
    {
        $reservation = new Reservation(collect(), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
    }

    /** @test */
    public function reservedTicketsAreReleasedWhenAReservationIsCancelled()
    {
        // $ticket1 = Mockery::mock(Ticket::class);
        // $ticket1->shouldReceive('release')->once();

        // $ticket2 = Mockery::mock(Ticket::class);
        // $ticket2->shouldReceive('release')->once();

        // $ticket3 = Mockery::mock(Ticket::class);
        // $ticket3->shouldReceive('release')->once();

        // $tickets = collect([
        //     Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(), 
        //     Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(), 
        //     Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(), 
        // ]);

        $tickets = collect([
            Mockery::spy(Ticket::class), 
            Mockery::spy(Ticket::class), 
            Mockery::spy(Ticket::class), 
        ]);


        $reservation = new Reservation($tickets, 'john@example.com');

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }

        /** @test */
        public function completingAReservation()
        {
            $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
            $tickets = factory(Ticket::class)->create(['concert_id' => $concert->id]);

            $reservation = new Reservation($tickets, 'john@example.com');

            $order = $reservation->complete();

            $this->assertEquals('john@example.com', $order->email());
            $this->assertEquals(3, $order->ticketQuantity());
            $this->assertEquals(3600, $order->amount);
        }
}
