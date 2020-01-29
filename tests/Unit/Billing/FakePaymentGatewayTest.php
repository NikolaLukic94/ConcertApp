<?php

namespace Tests\Unit;
use App\Billing\PaymentFailedException;
use App\Billing\FakePaymentGateway;
use App\Concert;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakePaymentGatewayTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function chargesWithValidPaymentTokenAreSuccessfull() 
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }   
    
    /** @test */
    public function chargesWithAnInvalidPaymentTokenFail() 
    {
        try {

            $paymentGateway = new FakePaymentGateway;

            $paymentGateway->charge(2500, 'invalid-payment-token');
    
        } catch (PaymentFailedException $e) {
            return;
        }

        $this->fail();
        
    }       
          
}
