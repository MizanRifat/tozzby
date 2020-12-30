<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix('paypal/standard')->group(function () {

        Route::get('/redirect', 'Webkul\Paypal\Http\Controllers\StandardController@redirect')->name('paypal.standard.redirect');

        Route::get('/success', 'Webkul\Paypal\Http\Controllers\StandardController@success')->name('paypal.standard.success');

        Route::get('/cancel', 'Webkul\Paypal\Http\Controllers\StandardController@cancel')->name('paypal.standard.cancel');
    });
});

Route::get('paypal/standard/ipn', 'Webkul\Paypal\Http\Controllers\StandardController@ipn')->name('paypal.standard.ipn');

Route::post('paypal/standard/ipn', 'Webkul\Paypal\Http\Controllers\StandardController@ipn')->name('paypal.standard.ipn');

Route::post('/api/payment/stripe', 'Webkul\Paypal\Http\Controllers\StripeController@paymentProcess');
Route::post('/api/stripe/payment_intents', 'Webkul\Paypal\Http\Controllers\StripeController@paymentIntent');
Route::get('/api/stripe/success', 'Webkul\Paypal\Http\Controllers\StripeController@success');
