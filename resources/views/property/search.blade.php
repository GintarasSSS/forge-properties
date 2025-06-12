<x-layouts.app>
    <form method="GET" action="{{ route('property.search') }}" class="mb-6 bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-3">
                <x-form.input name="location" label="Location" value="{{ request('location') }}" />
            </div>

            @php
                $sleepsOptions = [];
                $minSleeps = config('property.min_sleeps');
                $maxSleeps = config('property.max_sleeps');
                for ($i = $minSleeps; $i <= $maxSleeps; $i++) {
                    $sleepsOptions[$i] = $i;
                }

                $bedsOptions = [];
                $minBeds = config('property.min_beds');
                $maxBeds = config('property.max_beds');
                for ($i = $minBeds; $i <= $maxBeds; $i++) {
                    $bedsOptions[$i] = $i;
                }
            @endphp

            <div class="col-span-1 md:col-span-3 flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/2">
                    <x-form.select name="sleeps" label="Minimum Sleeps" :options="$sleepsOptions" :selected="request('sleeps')" />
                </div>
                <div class="w-full md:w-1/2">
                    <x-form.select name="beds" label="Minimum Beds" :options="$bedsOptions" :selected="request('beds')" />
                </div>
            </div>

            <div class="col-span-1 md:col-span-3 flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/2">
                    <x-form.input type="date" name="available_from" label="Available From" value="{{ request('available_from') }}" />
                </div>
                <div class="w-full md:w-1/2">
                    <x-form.input type="date" name="available_to" label="Available To" value="{{ request('available_to') }}" />
                </div>
            </div>

            <x-form.checkbox name="near_beach" label="Near Beach" :checked="request()->boolean('near_beach')" />
            <x-form.checkbox name="accepts_pets" label="Accepts Pets" :checked="request()->boolean('accepts_pets')" />
        </div>
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Search</button>
    </form>

    <div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($properties as $property)
                <x-property.card :property="$property" />
            @empty
                <div class="bg-yellow-100 text-yellow-800 p-4 rounded text-center col-span-1 md:col-span-3">
                    No properties found.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">{{ $properties->withQueryString()->links() }}</div>
</x-layouts.app>
