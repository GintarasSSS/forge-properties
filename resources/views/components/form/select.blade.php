@props(['name', 'label' => null, 'options' => [], 'selected' => null])

<div class="mb-4">
    @if($label)
        <label class="block mb-1 font-semibold" for="{{ $name }}">{{ $label }}</label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'shadow border rounded w-full py-2 px-3 text-gray-700']) }}
    >
        <option value="">-- Select --</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}"
                {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
