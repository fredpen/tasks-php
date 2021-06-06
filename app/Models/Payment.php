<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function payer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function totalRevenue ()
    {
        return Payment::all('chargedamount')->sum('chargedamount');
    }

}
