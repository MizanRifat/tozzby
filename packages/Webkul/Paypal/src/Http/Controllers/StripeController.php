<?php

namespace Webkul\Paypal\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use Illuminate\Support\Facades\Auth;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\API\Http\Resources\Sales\Order as OrderResource;



class StripeController extends Controller
{

    protected $orderRepository;


    public function __construct(OrderRepository $orderRepository)
    {

        $this->orderRepository = $orderRepository;
        $this->middleware('auth:api');
        auth()->setDefaultDriver('api');
    }



    public function paymentProcess(Request $request)
    {

        \Stripe\Stripe::setApiKey('sk_test_51GuKLVDkImmDuu4ZA2x6pl0MaKWPTg0UeaVtFUxJEEjXuy3S6QyOfkGZno9mSv2HqBn1GqHDIjvjUOlzstpXm4kD00gK6lu955');
        $token = $request->stripeToken;

        $charge = \Stripe\Charge::create([
            'amount' => 10000,
            'currency' => 'usd',
            'description' => 'Example Charge',
            'source' => $token
        ]);
    }

    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        return response()->json([
            'success' => true,
            'order'   => new OrderResource($order),
        ]);

    }


    public function paymentIntent(Request $request)
    {

        $cart = Cart::getCart();
        $amount = $cart->grand_total;

        \Stripe\Stripe::setApiKey('sk_test_51GuKLVDkImmDuu4ZA2x6pl0MaKWPTg0UeaVtFUxJEEjXuy3S6QyOfkGZno9mSv2HqBn1GqHDIjvjUOlzstpXm4kD00gK6lu955');

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => 'usd',
            // Verify your integration in this guide by including this parameter
            'metadata' => ['integration_check' => 'accept_a_payment'],
        ]);

        return $intent->client_secret;
    }
}
