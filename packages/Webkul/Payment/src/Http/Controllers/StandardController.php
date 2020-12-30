<?php

namespace Webkul\Payment\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;

class StandardController extends Controller
{
   
    protected $orderRepository;

  
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;

    }


    public function redirect()
    {
        // return view('paypal::standard-redirect');
    }

    public function cancel()
    {
        session()->flash('error', 'Paypal payment has been canceled.');

        // return redirect()->route('shop.checkout.cart.index');
    }


    public function success()
    {
        // $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        // Cart::deActivateCart();

        // session()->flash('order', $order);

        // return redirect()->route('shop.checkout.success');
    }

    public function paymentProcess(Request $request){
        \Stripe\Stripe::setApiKey('sk_test_51GuKLVDkImmDuu4ZA2x6pl0MaKWPTg0UeaVtFUxJEEjXuy3S6QyOfkGZno9mSv2HqBn1GqHDIjvjUOlzstpXm4kD00gK6lu955');
        $token = $request->stripeToken;

        $charge = \Stripe\Charge::create([
            'amount'=>10000,
            'currency' => 'usd',
            'description' => 'Example Charge',
            'source'=>$token
        ]);
        

        Session::flash('success', 'Payment successful!');
          
    }

 
}