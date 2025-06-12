<?php

namespace App\Repositories;

use App\Interfaces\PropertyRepositoryInterface;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class PropertyRepository implements PropertyRepositoryInterface
{
    private const PAGINATION_LIMIT = 12;

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Property::query();

        if ($location = Arr::get($filters, 'location')) {
            $query->whereHas('location', fn (Builder $q) => $q->where('name', 'like', '%' . $location . '%'));
        }

        if ($sleeps = Arr::get($filters, 'sleeps')) {
            $query->where('sleeps', '>=', $sleeps);
        }

        if ($beds = Arr::get($filters, 'beds')) {
            $query->where('beds', '>=', $beds);
        }

        if (!is_null($nearBeach = Arr::get($filters, 'near_beach'))) {
            $query->whereHas('location', fn (Builder $q) => $q->where('near_beach', $nearBeach));
        }

        if (!is_null($acceptsPets = Arr::get($filters, 'accepts_pets'))) {
            $query->where('accepts_pets', $acceptsPets);
        }

        $from = Arr::get($filters, 'available_from');
        $to = Arr::get($filters, 'available_to');

        if ($from || $to) {
            $from = $from ?? $to;
            $to = $to ?? $from;

            $query->whereDoesntHave('bookings', fn (Builder $q) =>
                $q->where('date_from', '<=', $to)
                    ->where('date_to', '>=', $from)
            );
        }

        return $query->paginate(self::PAGINATION_LIMIT)->withQueryString();
    }
}
