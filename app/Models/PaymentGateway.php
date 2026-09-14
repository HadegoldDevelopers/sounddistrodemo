<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = ['name', 'display_name', 'enabled', 'mode', 'note', 'settings'];

    protected $casts = [
        'enabled' => 'boolean',
        'settings' => 'array',
    ];
}
