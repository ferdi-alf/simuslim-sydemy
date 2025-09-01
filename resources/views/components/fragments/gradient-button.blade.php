@props([
    'type' => 'button',
    'disabled' => false,
])

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} @class([
    'cursor-pointer w-full text-lg font-semibold p-2 text-green-600 rounded-lg shadow-lg transition-all duration-200',
    'opacity-50 cursor-not-allowed' => $disabled,
    'hover:bg-green-600 hover:shadow-xl hover:scale-[1.02]' => !$disabled,
]) {{ $attributes }}>
    {{ $slot }}
</button>
