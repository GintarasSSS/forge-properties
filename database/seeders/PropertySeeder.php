<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    private const TOTAL = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locationIds = Location::pluck('id')->all();

        foreach ($locationIds as $locId) {
            Property::factory()->count(self::TOTAL)->create(['location_id' => $locId]);
        }
    }
}
