<?php

namespace Database\Seeders;

use App\Models\Performance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Performance::factory()->count(20)->create();
        Performance::factory()->count(5)->past()->create();
    }
}
