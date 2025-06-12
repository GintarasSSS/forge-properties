<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\Location;
use App\Models\Booking;
use App\Repositories\PropertyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testReturnsAllPropertiesWhenNoFilters()
    {
        Property::factory()->count(3)->create();

        $repo = new PropertyRepository();
        $results = $repo->search([]);

        $this->assertCount(3, $results);
    }

    public function testFiltersByLocation()
    {
        $locationName = 'Beach Side';

        $location = Location::factory()->create(['name' => $locationName]);
        $otherLocation = Location::factory()->create(['name' => 'Mountains']);

        $prop1 = Property::factory()->for($location)->create();
        Property::factory()->for($otherLocation)->create();

        $repo = new PropertyRepository();
        $results = $repo->search(['location' => $locationName]);

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($prop1));
        $this->assertEquals($locationName, $results->first()->location->name);
    }

    public function testFiltersByNearBeach()
    {
        $beachLocation = Location::factory()->create(['near_beach' => true]);
        $notBeachLocation = Location::factory()->create(['near_beach' => false]);

        $prop1 = Property::factory()->for($beachLocation)->create();
        $prop2 = Property::factory()->for($notBeachLocation)->create();

        $repo = new PropertyRepository();
        $results = $repo->search(['near_beach' => true]);

        $this->assertTrue($results->contains($prop1));
        $this->assertFalse($results->contains($prop2));
    }

    public function testFiltersByAcceptsPets()
    {
        $prop1 = Property::factory()->create(['accepts_pets' => true]);
        $prop2 = Property::factory()->create(['accepts_pets' => false]);

        $repo = new PropertyRepository();
        $results = $repo->search(['accepts_pets' => true]);

        $this->assertTrue($results->contains($prop1));
        $this->assertFalse($results->contains($prop2));
    }

    public function testFiltersBySleepsAndBeds()
    {
        $prop1 = Property::factory()->create(['sleeps' => 4, 'beds' => 2]);
        $prop2 = Property::factory()->create(['sleeps' => 2, 'beds' => 1]);

        $repo = new PropertyRepository();

        $results = $repo->search(['sleeps' => 3, 'beds' => 2]);

        $this->assertTrue($results->contains($prop1));
        $this->assertFalse($results->contains($prop2));
    }

    public function testFiltersByAvailableFromAndToIncludesPropertiesWithNoBookings()
    {
        $property = Property::factory()->create();
        $bookedProperty = Property::factory()->create();

        Booking::factory()->for($bookedProperty)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $repo = new PropertyRepository();

        $results = $repo->search([
            'available_from' => '2025-07-05',
            'available_to' => '2025-07-06',
        ]);

        $this->assertTrue($results->contains($property));
        $this->assertFalse($results->contains($bookedProperty));
    }

    public function testFiltersByAvailableFromOnly()
    {
        $property = Property::factory()->create();
        $booked = Property::factory()->create();

        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $repo = new PropertyRepository();

        $results = $repo->search([
            'available_from' => '2025-07-05',
        ]);

        $this->assertTrue($results->contains($property));
        $this->assertFalse($results->contains($booked));
    }

    public function testFiltersByAvailableToOnly()
    {
        $property = Property::factory()->create();
        $booked = Property::factory()->create();

        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $repo = new PropertyRepository();
        $results = $repo->search([
            'available_to' => '2025-07-07',
        ]);

        $this->assertTrue($results->contains($property));
        $this->assertFalse($results->contains($booked));
    }

    public function testReturnsEmptyWhenAllPropertiesAreBooked()
    {
        $booked = Property::factory()->create();

        Booking::factory()->for($booked)->create([
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-10',
        ]);

        $repo = new PropertyRepository();

        $results = $repo->search([
            'available_from' => '2025-07-05',
            'available_to' => '2025-07-07',
        ]);

        $this->assertCount(0, $results);
    }

    public function testReturnsAllWhenNoBookingsAndNoDateFilters()
    {
        $prop1 = Property::factory()->create();
        $prop2 = Property::factory()->create();

        $repo = new PropertyRepository();

        $results = $repo->search([]);

        $this->assertTrue($results->contains($prop1));
        $this->assertTrue($results->contains($prop2));
    }

    public function testDoesNotFailWithNoFilters()
    {
        Property::factory()->create();

        $repo = new PropertyRepository();

        $results = $repo->search([]);

        $this->assertNotNull($results);
    }
}
