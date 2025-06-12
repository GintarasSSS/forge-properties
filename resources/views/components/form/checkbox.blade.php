@props(['name', 'checked' => false, 'label'])

<div class="mb-4 flex items-center">
    <input value="1" type="checkbox" id="{{ $name }}" name="{{ $name }}" {{ old($name, $checked) ? 'checked' : '' }}>
    <label for="{{ $name }}" class="ml-2">{{ $label }}</label>
</div>

@error($name)
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror
