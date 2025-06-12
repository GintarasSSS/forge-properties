@props(['property'])

<div class="border rounded p-4 shadow mb-4 bg-white">
    <h2 class="text-lg font-bold">{{ $property->name }}</h2>
    <div>Location: {{ $property->location->name }} @if($property->location->near_beach) <span class="ml-2 text-xs text-blue-600">(Near Beach)</span>@endif</div>
    <div>Accepts Pets: {{ $property->accepts_pets ? 'Yes' : 'No' }}</div>
    <div>Sleeps: {{ $property->sleeps }}</div>
    <div>Beds: {{ $property->beds }}</div>
</div>
