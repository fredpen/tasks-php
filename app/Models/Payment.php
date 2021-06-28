<?php

namespace App\Models;

use App\Exceptions\PaymentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['deleted_at', 'access_code', 'payment_details'];

    public function payer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function totalRevenue()
    {
        return Payment::all('chargedamount')->sum('chargedamount');
    }

    public static function fetchUsingReference(string $ref): Payment
    {
        $payment = static::query()->where("reference", $ref);

        if (!$payment->count()) {
            throw PaymentException::invalidPaymentError();
        }

        $payment = $payment->where('status', 1);
        if (!$payment->count()) {
            throw PaymentException::invalidPaymentError("Value has aready been given to this reference");
        }

        return $payment->first();
    }
}
