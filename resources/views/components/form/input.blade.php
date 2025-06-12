@props(['type' => 'text', 'name', 'value' => '', 'label' => null])

<div class="mb-4">
    @if($label)
        <label class="block mb-1 font-semibold" for="{{ $name }}">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'shadow border rounded w-full py-2 px-3 text-gray-700']) }}
    />

    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
