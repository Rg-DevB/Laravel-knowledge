@props(['badge', 'size' => 'md', 'showLabel' => false])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
    ];
    
    $colorClasses = [
        'bronze' => 'bg-gradient-to-br from-amber-200 to-amber-600 text-white shadow-amber-300/50',
        'silver' => 'bg-gradient-to-br from-gray-300 to-gray-500 text-white shadow-gray-400/50',
        'gold' => 'bg-gradient-to-br from-yellow-300 to-yellow-600 text-white shadow-yellow-400/50',
        'platinum' => 'bg-gradient-to-br from-cyan-200 to-cyan-500 text-white shadow-cyan-300/50',
    ];
    
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentColor = $colorClasses[$badge->type] ?? $colorClasses['bronze'];
@endphp

<div 
    class="inline-flex items-center justify-center rounded-full {{ $currentSize }} {{ $currentColor }} shadow-lg"
    title="{{ $badge->description }}"
    {{ $attributes }}
>
    @if($badge->icon)
        <i class="{{ $badge->icon }}"></i>
    @else
        <span class="font-bold">{{ substr($badge->name, 0, 1) }}</span>
    @endif
    
    @if($showLabel)
        <span class="ml-2 font-medium">{{ $badge->name }}</span>
    @endif
</div>
