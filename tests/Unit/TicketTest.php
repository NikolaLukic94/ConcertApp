<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    /** @test */
    public function aTicketCanBeReleased()
    {
        $ticket = factory(Ticket::class)->create(['reserved_at' => Carbon::now()]);
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    public function aTicketCanBeReserved()
    {
        $ticket = factory(Ticket::class)->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();
        $this->assertNotNull($ticket->fresh()->reserved_at);
    }
}
