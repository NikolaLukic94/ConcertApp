<?php

namespace Tests\Feature;

use App\Concert;
use App\Billing\FakePaymentGateway;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PurchaseTicketTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->$paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /** @test */
    public function customerCanPurchaseConcertTickets()
    {
        // Create a concert
        $create = factory(Concert::class)->create(['ticket_price' => 3250]);
        // Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(201);
        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        // Make sure that an order exist for this customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());
    }    

    /** @test */
    public function emailIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            //'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }  

    /** @test */
    public function emailMustBeValidToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'not-an-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }     
    
    /** @test */
    public function ticketQuantityIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            //'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }     
    
    /** @test */
    public function ticketQuantityMustBeAtLeastOneToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }     
    
    /** @test */
    public function paymentIsRequired()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            //'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }     
}
