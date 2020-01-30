<?php

namespace Tests\Unit;
use App\Concert;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function canGetFormattedDate() 
    {
        // Create a concert with a known date. Since all fields are not nullable, we cannot just pass the date field and save a new concert        
        // So, use factory instead, and overwrite the date only
        
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8pm'),
        ]);

        // Retrieve the formatted date
        // Verify the date is formatted as expected
        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */
    public function canGetFormattedStartTime() 
    {
        
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }    
    
    /** @test */
    public function canGetTicketPriceInDollars() 
    {
        
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }     

    /** @test */
    public function concertsWithAPublishedAtDateArePublished() 
    {
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = factory(Concert::class)->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }     
    
    /** @test */
    public function canOrderConcertTickets() 
    {
        $concert = factory(Concert::class)->create()->addTickets(3);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
    }  
    
    /** @test */
    public function canAddTickets() 
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }   
    
    /** @test */
    public function ticketsRemainingDoesNotIncludeTicketsAssociatedWithAnOrder() 
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketRemaining());
    }     

    /** @test */
    public function tryingToPurchaseMoreTicketsThanRemainThrowsAnException() 
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        try {
            $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Order succeeded even though there were not enough tickets remaining.");
    }        
    
        /** @test */
        public function cannotOrderTicketsThatHaveAlreadyBeenPurchased() 
        {
            $concert = factory(Concert::class)->create();
            $concert->addTickets(10);
            $concert->orderTickets('john@example.com', 8);

            try {
                $concert->orderTickets('john@example.com', 3);
            } catch (NotEnoughTicketsException $e) {
                $johnsOrder = $concert->orders()->where('email', 'john@example.com')->first();
                $this->assertNull($johnsOrder);
                $this->assertEquals(2, $concert->ticketsRemaining());
                return;
            }     
            
            $this->fail("Order succeeded even though there were not enough tickets remaining.");

        }  
}
