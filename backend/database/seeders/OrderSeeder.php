<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyer1 = User::where('email', 'buyer@mail.com')->first();

        // ======================
        // Buyer 1 Order
        // ======================
        Order::create([
            'user_id' => $buyer1->id,
            'invoice_number' => 'INV-001',
            'total_price' => 120000,
            'status' => 'paid',
        ]);
    }
}
