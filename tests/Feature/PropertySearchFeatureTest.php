<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Location;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertySearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testSearchEndpointReturnsPropertiesByLocation()
    {
        $location = Location::factory()->create(['name' => 'Coastal']);
        $prop1 = Property::factory()->for($location)->create();

        $otherLocation = Location::factory()->create(['name' => 'Inland']);
        $prop2 = Property::factory()->for($otherLocation)->create();

        $response = $this->get('/?location=Coastal');

        $response->assertStatus(200);
        $response->assertSee($prop1->name);
        $response->assertDontSee($prop2->name);
    }

    public function testSearchEndpointFiltersByNearBeach()
    {
        $beachLocation = Location::factory()->create(['near_beach' => true]);
        $notBeachLocation = Location::factory()->create(['near_beach' => false]);

        $prop1 = Property::factory()->for($beachLocation)->create();
        $prop2 = Property::factory()->for($notBeachLocation)->create();

        $response = $this->get('/?near_beach=1');

        $response->assertStatus(200);
        $response->assertSee($prop1->name);
        $response->assertDontSee($prop2->name);
    }

    public function testSearchEndpointFiltersByAcceptsPets()
    {
        $prop1 = Property::factory()->create(['accepts_pets' => true]);
        $prop2 = Property::factory()->create(['accepts_pets' => false]);

        $response = $this->get('/?accepts_pets=1');

        $response->assertStatus(200);
        $response->assertSee($prop1->name);
        $response->assertDontSee($prop2->name);
    }

    public function testSearchEndpointFiltersBySleepsAndBeds()
    {
        $prop1 = Property::factory()->create(['sleeps' => 6, 'beds' => 3]);
        $prop2 = Property::factory()->create(['sleeps' => 2, 'beds' => 1]);

        $response = $this->get('/?sleeps=5&beds=2');

        $response->assertStatus(200);
        $response->assertSee($prop1->name);
        $response->assertDontSee($prop2->name);
    }

    public function testSearchEndpointFiltersByAvailableFromAndTo()
    {
        Property::factory()->create(['name' => 'Available']);
        $booked = Property::factory()->create(['name' => 'Booked']);

        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $response = $this->get('/?available_from=2025-07-05&available_to=2025-07-06');

        $response->assertStatus(200);
        $response->assertSee('Available');
        $response->assertDontSee('Booked');
    }

    public function testSearchEndpointReturnsEmptyWhenAllAreBooked()
    {
        $booked = Property::factory()->create(['name' => 'Booked']);
        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $response = $this->get('/?available_from=2025-07-05&available_to=2025-07-07');

        $response->assertStatus(200);
        $response->assertDontSee('Booked');
    }

    public function testSearchEndpointReturnsAllWhenNoBookingsAndNoFilters()
    {
        Property::factory()->create(['name' => 'One']);
        Property::factory()->create(['name' => 'Two']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('One');
        $response->assertSee('Two');
    }

    public function testSearchEndpointHandlesNoFilters()
    {
        $property = Property::factory()->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($property->name);
    }
}
