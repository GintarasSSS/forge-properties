<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $from = Carbon::now()->addDays($this->faker->numberBetween(1, 30));
        $to = (clone $from)->addDays($this->faker->numberBetween(2, 14));

        return [
            'property_id' => Property::factory(),
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
        ];
    }
}
