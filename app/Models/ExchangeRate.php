<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['country_id', 'currency_code', 'exchange_rate_to_usd', 'trend', 'change_24h'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}