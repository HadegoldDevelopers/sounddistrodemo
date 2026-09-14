<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 
        'role', 
        'price', 
        'billing_cycle', 
        'description', 
        'currency'
        
        ];
        
        protected $casts = [
    'price' => 'decimal:2',
];

public function expiryDate()
{
    switch ($this->billing_cycle) {
        case 'monthly':
            return now()->addMonth();

        case 'quarterly':
            return now()->addMonths(3);

        case 'yearly':
            return now()->addYear();

        default:
            return now()->addMonth();
    }
}


}
