<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // \App\Models\Category::factory(5)->create();
        // \App\Models\Product::factory(20)->create();
         //\App\Models\User::factory(20)->create();
        // \App\Models\Customer::factory(20)->create();

        // \App\Models\Order::factory(20)->create()->each(function($order){
        //     $order->details()->create([
        //         'order_id' => $order->id,
        //         'product_id' => Product::all()->random()->id,
        //         'quantity' => $order->items,
        //         'price' => $order->total / $order->items
        //     ]);
        // });
        $this->call(PermisosSeeder::class);
        $this->call(UserSeeder::class);

    }
}
