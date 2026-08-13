<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Manufacturer::insert([
            ['name' => 'Dell', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lenovo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asus', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
