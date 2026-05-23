<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'High performance laptop',
            'quantity' => 10,
            'price' => 1500
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Modern smartphone',
            'quantity' => 20,
            'price' => 800
        ]);

        Product::create([
            'name' => 'Headphones',
            'description' => 'Wireless headphones',
            'quantity' => 15,
            'price' => 200
        ]);

        Product::create([
            'name' => 'Keyboard',
            'description' => 'Mechanical keyboard',
            'quantity' => 25,
            'price' => 120
        ]);

        Product::create([
            'name' => 'Mouse',
            'description' => 'Wireless mouse',
            'quantity' => 30,
            'price' => 60
        ]);
    }
}