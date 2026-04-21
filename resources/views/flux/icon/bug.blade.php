{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
    'variant' => 'outline',
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp

<svg
    {{ $attributes->class($classes) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
>
    <circle cx="8" cy="6" r="2" />
    <circle cx="16" cy="6" r="2" />
    <path d="m10.8 12.8 1.2-1.2" />
    <path d="m13.2 12.8-1.2-1.2" />
    <path d="M12 10v4" />
    <path d="m8 14 2-2" />
    <path d="m16 14-2-2" />
    <path d="M7.5 8h8.5" />
    <path d="m6 10 2 2" />
    <path d="m18 10-2 2" />
    <rect x="4" y="12" width="16" height="8" rx="2" />
</svg>