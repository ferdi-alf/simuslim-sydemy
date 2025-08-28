@props([
    'type' => 'button',
    'disabled' => false,
])

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} @class([
    'bg-gradient-to-br cursor-pointer w-full text-lg font-semibold p-2 focus:ring-4 focus:ring-indigo-300 from-indigo-200 via-indigo-600 text-white rounded-lg shadow-lg to-indigo-400 transition-all duration-200',
    'opacity-50 cursor-not-allowed' => $disabled,
    'hover:shadow-xl hover:scale-[1.02]' => !$disabled,
]) {{ $attributes }}>
    {{ $slot }}
</button>
