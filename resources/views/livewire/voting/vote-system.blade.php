<div class="flex flex-col items-center gap-1">
    <button wire:click="vote(1)"
            @class([
                'w-8 h-8 rounded-lg flex items-center justify-center transition-all',
                'bg-emerald-500/20 text-emerald-400' => $userVote === 1,
                'bg-zinc-800 text-zinc-500 hover:bg-zinc-700 hover:text-zinc-300' => $userVote !== 1,
            ])>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"/></svg>
    </button>

    <span @class([
        'text-sm font-bold min-w-[2rem] text-center',
        'text-emerald-400' => $votesCount > 0,
        'text-red-400' => $votesCount < 0,
        'text-zinc-400' => $votesCount === 0,
    ])>{{ $votesCount }}</span>

    <button wire:click="vote(-1)"
            @class([
                'w-8 h-8 rounded-lg flex items-center justify-center transition-all',
                'bg-red-500/20 text-red-400' => $userVote === -1,
                'bg-zinc-800 text-zinc-500 hover:bg-zinc-700 hover:text-zinc-300' => $userVote !== -1,
            ])>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
    </button>
</div>
