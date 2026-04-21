<div x-data="{ show: false }" class="relative">
    <input type="text" wire:model.live.debounce.300ms="query"
           placeholder="Search problems..."
           @focus="show = true"
           @click.away="show = false"
           @keydown.escape="show = false"
           class="w-full px-4 py-2 rounded-xl bg-zinc-900/80 border border-zinc-700/50 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500/50 text-sm">

    @if($showSuggestions && count($suggestions) > 0)
    <div x-show="show" x-cloak
         class="absolute top-full left-0 right-0 mt-2 bg-zinc-900 border border-zinc-700 rounded-xl shadow-2xl overflow-hidden z-50">
        @foreach($suggestions as $suggestion)
        <a href="{{ route('problems.show', $suggestion['slug']) }}" wire:navigate
           class="flex items-center gap-3 px-4 py-3 hover:bg-zinc-800 transition-colors border-b border-zinc-800/50 last:border-0">
            <span @class([
                'shrink-0 px-1.5 py-0.5 rounded text-[10px] font-semibold',
                'bg-blue-400/10 text-blue-400' => $suggestion['status'] === 'open',
                'bg-emerald-400/10 text-emerald-400' => $suggestion['status'] === 'solved',
            ])>{{ $suggestion['status'] }}</span>
            <span class="flex-1 text-sm text-zinc-300 truncate">{{ $suggestion['title'] }}</span>
            <span class="text-xs text-zinc-600">{{ $suggestion['votes_count'] ?? 0 }} votes</span>
        </a>
        @endforeach
    </div>
    @endif
</div>
