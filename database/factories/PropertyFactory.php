<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName,
            'location_id' => Location::factory(),
            'accepts_pets' => $this->faker->boolean,
            'sleeps' => $this->faker->numberBetween(config('property.min_sleeps'), config('property.max_sleeps')),
            'beds' => $this->faker->numberBetween(config('property.min_beds'), config('property.max_beds'))
        ];
    }
}
