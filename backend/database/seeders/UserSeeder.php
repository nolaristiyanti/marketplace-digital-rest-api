<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Seller A',
            'email' => 'sellerA@mail.com',
            'password' => 'password',
            'role' => 'seller',
            'balance' => 200000,
        ]);

        User::create([
            'name' => 'Seller B',
            'email' => 'sellerB@mail.com',
            'password' => 'password',
            'role' => 'seller',
            'balance' => 150000,
        ]);

        User::create([
            'name' => 'Buyer 1',
            'email' => 'buyer@mail.com',
            'password' => 'password',
            'role' => 'buyer',
            'balance' => 500000,
        ]);
    }
}
