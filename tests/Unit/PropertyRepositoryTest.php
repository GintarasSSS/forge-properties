<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Property;
use App\Repositories\PropertyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PropertyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private const BOOKING_DATES = [
        'date_from' => '2025-07-01',
        'date_to' => '2025-07-10'
    ];

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

    #[DataProvider('BookedPropertyDatesNotAvailable')]
    public function testDoesNotReturnBookedButReturnsNotBookedPropertiesWithSpecificDateFilters(array $filters): void
    {
        $property = Property::factory()->create();
        $booked = Property::factory()->create();

        Booking::factory()->for($booked)->create(self::BOOKING_DATES);

        $repo = new PropertyRepository();
        $results = $repo->search($filters);

        $this->assertTrue($results->contains($property));
        $this->assertFalse($results->contains($booked));
    }

    public static function BookedPropertyDatesNotAvailable(): array
    {
        return [
            'available_from within booking dates' => [[
                'available_from' => '2025-07-05',
            ]],
            'available_to within booking dates' => [[
                'available_to' => '2025-07-07',
            ]],
            'available_from and available_to within booking dates' => [[
                'available_from' => '2025-07-05',
                'available_to' => '2025-07-06',
            ]],
            'available_from and available_to equal to booking dates' => [[
                'available_from' => '2025-07-01',
                'available_to' => '2025-07-10',
            ]],
            'available_from within and available_to after booking dates' => [[
                'available_from' => '2025-07-05',
                'available_to' => '2025-07-15',
            ]],
            'available_to equal to booking start date' => [[
                'available_to' => '2025-07-01'
            ]],
            'available_to equal to booking end date' => [[
                'available_to' => '2025-07-10'
            ]],
            'available_from equal to booking start date' => [[
                'available_from' => '2025-07-01'
            ]],
            'available_from equal to booking end date' => [[
                'available_from' => '2025-07-10'
            ]],
            'available_from before and available_to within booking dates' => [[
                'available_from' => '2025-06-30',
                'available_to' => '2025-07-05',
            ]]
        ];
    }

    #[DataProvider('bookedPropertyDatesAvailable')]
    public function testReturnsBookedPropertyWithSpecificDateFilters(array $filters): void
    {
        $booked = Property::factory()->create();

        Booking::factory()->for($booked)->create(self::BOOKING_DATES);

        $repo = new PropertyRepository();

        $results = $repo->search($filters);

        $this->assertCount(1, $results);
    }

    public static function bookedPropertyDatesAvailable(): array
    {
        return [
            'available_to after end of booking' => [[
                'available_to' => '2025-07-11'
            ]],
            'available_to before start of booking' => [[
                'available_to' => '2025-06-31'
            ]],
            'available_from after end of booking' => [[
                'available_from' => '2025-07-11'
            ]],
            'available_from before start of booking' => [[
                'available_from' => '2025-06-31'
            ]],
            'available_from and available_to after end of booking' => [[
                'available_from' => '2025-07-11',
                'available_to' => '2025-07-12',
            ]],
            'available_from and available_to before start of booking' => [[
                'available_from' => '2025-06-30',
                'available_to' => '2025-06-31',
            ]]
        ];
    }

    #[DataProvider('notBookedPropertiesDates')]
    public function testReturnsAllWhenNoBookingsAndSpecificDateFilters(array $filters): void
    {
        $prop1 = Property::factory()->create();
        $prop2 = Property::factory()->create();

        $repo = new PropertyRepository();

        $results = $repo->search($filters);

        $this->assertTrue($results->contains($prop1));
        $this->assertTrue($results->contains($prop2));
    }

    public static function notBookedPropertiesDates(): array
    {
        return [
            'available_from only' => [
                ['available_from' => '2025-07-01']
            ],
            'available_to only' => [
                ['available_to' => '2025-07-10']
            ],
            'available_from and available_to' => [
                [
                    'available_from' => '2025-07-01',
                    'available_to' => '2025-07-10'
                ]
            ],
            'no filters' => [[]]
        ];
    }

    public function testDoesNotFailWithNoFilters()
    {
        Property::factory()->create();

        $repo = new PropertyRepository();

        $results = $repo->search([]);

        $this->assertNotNull($results);
    }
}
