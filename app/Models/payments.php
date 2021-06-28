<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['payment_details'];

    protected $casts = [
        'payment_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(course::class);
    }

    public static function initialisePayment($course, $quantity, $isInitiator = false, $isTest = false)
    {
        return $isTest ? static::initialisePaymentTest($course, $quantity, $isInitiator) :
            static::initialisePaymentForFrontend($course, $quantity, $isInitiator);
    }

    private static function initialisePaymentTest($course, $quantity, $isInitiator): array
    {
        $reference = time();
        $token = config("Eyo.pay.test_Secret_key");
        $url = "https://api.paystack.co/transaction/initialize";
        $amount = $isInitiator ? $course->initiator_amount : ($course->contributor_amount * $quantity);
        $paystackAmount = round($amount * 100); //paystack works in kobo

        $response = Http::withToken($token)->post($url, [
            "amount" => $paystackAmount,
            "reference" => $reference,
            "email" => Auth::user()->email,
        ]);

        if ($response->failed()) {
            throw PaymentException::providerError($response->object()->message);
        }

        $paystackObject = $response->object()->data;
        $payment = Payment::create([
            'user_id' => Auth::user()->id,
            'course_id' => $course->id,
            'reference' => $paystackObject->reference,
            'access_code' => $paystackObject->access_code,
            'quantity' => $isInitiator ? 1 : $quantity,
            'amount_paid' => $amount,
            'authorization_url' => $paystackObject->authorization_url,
        ]);

        if (!$payment) {
            throw PaymentException::paymentCreationError();
        }

        return  [
            'currency' => "NGN",
            'amount_to_be_paid' => $paystackAmount,
            'reference' => $payment->reference,
            'authorization_url' => $paystackObject->authorization_url,
        ];
    }

    private static function initialisePaymentForFrontend(object $course, $quantity, $isInitiator): array
    {
        $reference = time();
        $quantity =  $quantity ? $quantity : 1;
        $amount = $isInitiator ? $course->initiator_amount : ($course->contributor_amount * $quantity);
        $paystackAmount = round($amount * 100); //paystack works in kobo

        $payment = Payment::create([
            'user_id' => Auth::user()->id,
            'course_id' => $course->id,
            'reference' => $reference,
            'quantity' => $isInitiator ? 1 : $quantity,
            'amount_paid' => $amount
        ]);

        if (!$payment) {
            throw PaymentException::paymentCreationError();
        }

        return [
            'currency' => "NGN",
            'quantity' => $payment->quantity,
            'reference' => $payment->reference,
            'email' => Auth::user()->email,
            'key' => config("Eyo.pay.test_Public_key"),
            'amount_to_be_paid' => $paystackAmount
        ];
    }

    public static function fetchPayment($ref): Payment
    {
        $payment = static::query()
            ->where("payment_status", 1)
            ->where("reference", $ref)
            ->first();

        if (!$payment) {
            throw PaymentException::paymentCreationError();
        }

        return $payment;
    }
}
