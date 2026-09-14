<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaysSeeder extends Seeder
{
    public function run()
    {
        $gateways = [
            ['name' => 'paystack', 'display_name' => 'Paystack'],
            ['name' => 'coinpayments', 'display_name' => 'CoinPayments'],
            ['name' => 'nowpayment', 'display_name' => 'NowPayment'],
            ['name' => 'paypal', 'display_name' => 'PayPal'],
            ['name' => 'manual', 'display_name' => 'Manual Payment'],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['name' => $gateway['name']],
                ['display_name' => $gateway['display_name']]
            );
        }
    }
}
