<?php

namespace Webkul\Payment\Payment;

class StripePayment extends Payment
{
    

    
 
    protected $code  = 'stripepayment';

    public function getRedirectUrl()
    {
        return '/checkout/card_payment';
    }
}