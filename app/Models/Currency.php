<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'country',
        'conversion_rate',
        'is_active'
    ];

    public static function active()
    {
        return self::where('is_active', true)->get();
    }
}
