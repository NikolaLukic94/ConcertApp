<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function useCanViewAPublishedConcertListing()
    {
        //arrange
            //create a concert

        $concert = factory(Concert::class)->states('published')->create([
            'title' => 'SOAD',
            'subtitle' => 'Rock@Ring',
            'date' => Carbon::parse('December 13, 2019'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '13223',
            'additional_information' => 'For tickets, call 555-888-3333',
        ]);

        //act
            //view a concert listing
        $this->visit('.concerts.'. $concert->id);
        //assert
            //see the concert details
        $this->see('SOAD');
        $this->see('Rock@Ring');
        $this->see('December 13, 2019');
        $this->see('3250');
        $this->see('The Mosh Pit');
        $this->see('123 Example Lane');
        $this->see('Laraville');
        $this->see('ON');
        $this->see('13223');
        $this->see('For tickets, call 555-888-3333');
    }

    /** @test */
    public function userCannotViewUnpublishedConcertListings()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $this->get('/concerts/' . $concert->id);

        $this->assertResponseStatus(404);
    }    
}
