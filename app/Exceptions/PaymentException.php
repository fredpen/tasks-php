<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public static function providerError($message)
    {
        return new static($message);
    }

    public static function paymentCreationError()
    {
        return new static("We could not perform this action at the moment, try again");
    }

    public static function invalidPaymentError($message = "Invalid payment reference")
    {
        return new static($message);
    }

    public static function paystackError($e)
    {
        return new static($e);
    }

    public static function incompletePayment()
    {
        return new static("Incomplete transaction at paystack");
    }

    public static function insufficientPayment()
    {
        return new static("Amount paid is less than the product amount, hence no value was given");
    }

    public static function valueAlreadyGiven()
    {
        return new static("Payment reference has already been fulfilled or invalid");
    }
}
