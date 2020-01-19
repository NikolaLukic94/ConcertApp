<?php

namespace Tests\Unit;
use App\Concert;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConcertTest extends TestCase
{
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
}