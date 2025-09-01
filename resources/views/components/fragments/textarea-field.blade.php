@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'rows' => 4,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }} {{ $required ? '<span class="text-red-500">*</span>' : '' }}
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900 text-sm">{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
