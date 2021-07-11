<?php

namespace App\Helpers;

use App\Exceptions\PaymentException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PaystackHelper
{
    public static function init($amount, $email)
    {
        $reference = time();
        $baseUrl = Config::get('app.url');
        $paystackAmount = round($amount * 100); //paystack works in kobo
        $token = Config::get('rave.payments.test_Secret_key');
        $endpoint = "https://api.paystack.co/transaction/initialize";
        $redirect_url = "{$baseUrl}/api/project/payment/verify";

        $response = Http::withToken($token)->post($endpoint, [
            "amount" => $paystackAmount,
            "reference" => $reference,
            "currency" => 'NGN',
            "email" => $email,
            "callback_url" => $redirect_url
        ]);

        if ($response->failed()) {
            throw PaymentException::providerError($response->object()->message);
        }

        return $response->collect()['data'];
    }

    public static function verfiy(string $reference, $amount)
    {
        $token = Config::get('rave.payments.test_Secret_key');
        $url = "https://api.paystack.co/transaction/verify/{$reference}";

        $response = Http::withToken($token)->get($url);
         $paymentDetails = $response->object();

        if ($response->failed()) {
            throw PaymentException::paystackError($paymentDetails->message);
        }

        if ($paymentDetails->data->status != "success") {
            throw PaymentException::incompletePayment();
        }

        $amountPaid = $paymentDetails->data->amount / 100; //paystack works in kobo
        if ($amountPaid < $amount) {
            throw PaymentException::insufficientPayment();
        }

        return true;
    }


}
