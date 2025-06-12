<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    private const TOTAL = 20;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyIds = Property::pluck('id')->all();

        Booking::factory()
            ->count(self::TOTAL)
            ->make()
            ->each(function ($booking) use ($propertyIds) {
                $booking->property_id = fake()->randomElement($propertyIds);
                $booking->save();
            });
    }
}
