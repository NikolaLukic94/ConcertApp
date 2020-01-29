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

    public function orderTickets($concert, $params)
    {
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    public function assertValidationError($field)
    {
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey($field, $this->decodeResponseJson());       
    }

    /** @test */
    public function customerCanPurchaseConcertTicketsToAPublicConcert()
    {
        // Create a concert
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);
        $concert->addTickets(3);
        // Purchase concert tickets
        $this->orderTickets($concert, [
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
    public function cannotPurchaseTicketsToAnUnpublishedConcert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        
        $this->assertResponseStatus(401);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }  

    /** @test */
    public function cannotPurchaseMoreTicketsThanRemains() 
    {
        $concert = factory(Concert::class)->states('published')->create();
        $concert->addTickets(50);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);   
        
        $this->assertResponseStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function emailIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [        
            //'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertValidationError('email');
    }  

    /** @test */
    public function emailMustBeValidToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [  
            'email' => 'not-an-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertValidationError('email');
    }     
    
    /** @test */
    public function ticketQuantityIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [  
            'email' => 'john@example.com',
            //'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertValidationError('ticket_quantity');
    }     
    
    /** @test */
    public function ticketQuantityMustBeAtLeastOneToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [  
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertValidationError('ticket_quantity');
    }     
    
    /** @test */
    public function paymentIsRequired()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [  
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            //'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);      
        
        $this->assertValidationError('payment_token');
    }
    
    /** @test */
    public function anOrderIsNotCreatedIfPaymentFailed()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $concert->addTickets(3);


        $this->orderTickets($concert, [  
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);      
        
    }        
}
