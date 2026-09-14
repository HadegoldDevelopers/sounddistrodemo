<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubscriptionPlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('subscription_plans')->delete();
        
        \DB::table('subscription_plans')->insert(array (
            0 => 
            array (
                'id' => 3,
                'name' => 'Artist',
                'role' => 'artist',
                'price' => '10.42',
                'currency' => 'USD',
                'billing_cycle' => 'yearly',
                'description' => NULL,
                'created_at' => '2025-07-31 07:10:20',
                'updated_at' => '2025-07-31 07:10:20',
            ),
        ));
        
        
    }
}