@props(['name'])

@php
$icons = [
    'home' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline points="9,22 9,12 15,12 15,22" />',
    'bug' => '<circle cx="8" cy="6" r="2" /><circle cx="16" cy="6" r="2" /><path d="M12 10v4" /><rect x="4" y="12" width="16" height="8" rx="2" /><path d="M7.5 8h8.5" />',
    'plus-circle' => '<circle cx="12" cy="12" r="10" /><path d="M12 8v8" /><path d="M8 12h8" />',
    'chart' => '<path d="M3 3v18h18" /><path d="m19 9-5 5-4-4-3 3" />',
];
$path = $icons[$name] ?? '';
@endphp

<svg {{ $attributes->merge(['class' => 'w-4 h-4 flex-shrink-0']) }}
     xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 24 24"
     fill="none"
     stroke="currentColor"
     stroke-width="2"
     stroke-linecap="round"
     stroke-linejoin="round"
     aria-hidden="true">
    {!! $path !!}
</svg>