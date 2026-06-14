@props(['type', 'message', 'icon' => false])

<div {{ $attributes->merge(['class' => "alert alert-{$type}"]) }}>
    @if($icon)
        <x-icon />
    @endif
    {{ $message }}
</div>
