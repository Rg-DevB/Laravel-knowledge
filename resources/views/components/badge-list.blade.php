@props(['badges', 'limit' => null])

@php
    $displayBadges = $limit ? $badges->take($limit) : $badges;
@endphp

<div class="flex flex-wrap gap-2" {{ $attributes }}>
    @foreach($displayBadges as $badge)
        <x-badge 
            :badge="$badge" 
            :title="$badge->description"
            class="transform hover:scale-110 transition-transform duration-200"
        />
    @endforeach
    
    @if($limit && $badges->count() > $limit)
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
            +{{ $badges->count() - $limit }} autres
        </span>
    @endif
</div>
