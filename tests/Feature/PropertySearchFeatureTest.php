<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Location;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class PropertySearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testSearchEndpointReturnsPropertiesByLocation(): void
    {
        $location = Location::factory()->create(['name' => 'Coastal']);
        $prop1 = Property::factory()->for($location)->create();

        $otherLocation = Location::factory()->create(['name' => 'Inland']);
        $prop2 = Property::factory()->for($otherLocation)->create();

        $response = $this->get('/?location=Coastal');

        $this->filtersAssertion($response, $prop1->name, $prop2->name);
    }

    public function testSearchEndpointFiltersByNearBeach(): void
    {
        $beachLocation = Location::factory()->create(['near_beach' => true]);
        $notBeachLocation = Location::factory()->create(['near_beach' => false]);

        $prop1 = Property::factory()->for($beachLocation)->create();
        $prop2 = Property::factory()->for($notBeachLocation)->create();

        $response = $this->get('/?near_beach=1');

        $this->filtersAssertion($response, $prop1->name, $prop2->name);
    }

    public function testSearchEndpointFiltersByAcceptsPets(): void
    {
        $prop1 = Property::factory()->create(['accepts_pets' => true]);
        $prop2 = Property::factory()->create(['accepts_pets' => false]);

        $response = $this->get('/?accepts_pets=1');

        $this->filtersAssertion($response, $prop1->name, $prop2->name);
    }

    public function testSearchEndpointFiltersBySleepsAndBeds(): void
    {
        $prop1 = Property::factory()->create(['sleeps' => 6, 'beds' => 3]);
        $prop2 = Property::factory()->create(['sleeps' => 2, 'beds' => 1]);

        $response = $this->get('/?sleeps=5&beds=2');

        $this->filtersAssertion($response, $prop1->name, $prop2->name);
    }

    public function testSearchEndpointFiltersByAvailableFromAndTo(): void
    {
        Property::factory()->create(['name' => 'Available']);
        $booked = Property::factory()->create(['name' => 'Booked']);

        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $response = $this->get('/?available_from=2025-07-05&available_to=2025-07-06');

        $this->filtersAssertion($response, 'Available', 'Booked');
    }

    public function testSearchEndpointReturnsEmptyWhenAllAreBooked(): void
    {
        $booked = Property::factory()->create(['name' => 'Booked']);
        $notBooked = Property::factory()->create();

        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $response = $this->get('/?available_from=2025-07-05&available_to=2025-07-07');

        $this->filtersAssertion($response, $notBooked->name, 'Booked');
    }

    public function testSearchEndpointReturnsAllWhenNoBookingsAndNoFilters(): void
    {
        Property::factory()->create(['name' => 'One']);
        Property::factory()->create(['name' => 'Two']);

        $response = $this->get('/');

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertSee('One');
        $response->assertSee('Two');
    }

    public function testSearchEndpointHandlesNoFilters(): void
    {
        $property = Property::factory()->create();

        $response = $this->get('/');

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertSee($property->name);
    }

    private function filtersAssertion(TestResponse $response, string $existent, string $notExistent): void
    {
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertSee($existent);
        $response->assertDontSee($notExistent);
    }
}
