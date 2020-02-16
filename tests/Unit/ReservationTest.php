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

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
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


            $reservation = new Reservation($tickets);

            $reservation->cancel();

            foreach ($tickets as $ticket) {
                $ticket->shouldHaveReceived('release');
            }
        }
}
