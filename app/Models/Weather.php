<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    protected $fillable = [
        'country_id',
        'temperature',
        'rainfall',
        'wind_speed',
        'storm_risk'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}