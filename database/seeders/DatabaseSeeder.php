<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       

User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
]);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(SubscriptionPlansTableSeeder::class);
    }
}
