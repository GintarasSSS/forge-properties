<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    private const TOTAL = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::factory()->count(self::TOTAL)->create();
    }
}
