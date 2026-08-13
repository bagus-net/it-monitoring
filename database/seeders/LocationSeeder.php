<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Location::insert([
            ['name' => 'Ruang Server', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruang IT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lantai 1', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lantai 2', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
