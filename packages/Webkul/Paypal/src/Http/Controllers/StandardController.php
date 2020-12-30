<?php

namespace Webkul\Paypal\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Paypal\Helpers\Ipn;

class StandardController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Ipn object
     *
     * @var \Webkul\Paypal\Helpers\Ipn
     */
    protected $ipnHelper;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Paypal\Helpers\Ipn  $ipnHelper
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        Ipn $ipnHelper
    ) {
        $this->orderRepository = $orderRepository;

        $this->ipnHelper = $ipnHelper;
    }

    /**
     * Redirects to the paypal.
     *
     * @return \Illuminate\View\View
     */
    public function redirect()
    {
        return view('paypal::standard-redirect');
    }

    /**
     * Cancel payment from paypal.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', 'Paypal payment has been canceled.');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }

    /**
     * Paypal Ipn listener
     *
     * @return \Illuminate\Http\Response
     */
    public function ipn()
    {
        $this->ipnHelper->processIpn(request()->all());
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


        Session::flash('success', 'Payment successful!');
    }
   
}
