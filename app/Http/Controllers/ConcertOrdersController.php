<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => 'required',
            'ticket_quantity' => ['required', 'integer', 'null'],
            'payment_token' => ['required']
        ]);

        try {

            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

            $this->paymentGateway->charge($reservation->totalCost(), $request('payment_token'));

            $order = $reservation->complete();

            return response()->json($order, 201);    
        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }

    }
}
