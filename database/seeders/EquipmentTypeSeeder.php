<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\EquipmentType::insert([
            ['name' => 'Komputer', 'description' => 'Desktop / Laptop', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monitor', 'description' => 'Layar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Printer', 'description' => 'Printer / Scanner', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Network', 'description' => 'Switch, Router, Access Point', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
