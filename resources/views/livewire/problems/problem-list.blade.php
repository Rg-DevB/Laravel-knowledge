<div class="space-y-2">
    @forelse($this->problems as $problem)
    <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
       class="flex items-start gap-3 p-4 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 hover:border-zinc-700/60 transition-all group">

        <span @class([
            'mt-0.5 shrink-0 inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold',
            'bg-blue-400/10 text-blue-400 ring-1 ring-blue-400/20'    => $problem->status === 'open',
            'bg-emerald-400/10 text-emerald-400 ring-1 ring-emerald-400/20' => $problem->status === 'solved',
            'bg-zinc-400/10 text-zinc-400 ring-1 ring-zinc-400/20'      => $problem->status === 'closed',
            'bg-yellow-400/10 text-yellow-400 ring-1 ring-yellow-400/20' => $problem->status === 'duplicate',
        ])>{{ ucfirst($problem->status) }}</span>

        <div class="flex-1 min-w-0">
            <p class="text-sm text-zinc-300 group-hover:text-white transition-colors line-clamp-2">{{ $problem->title }}</p>
            <div class="flex items-center flex-wrap gap-3 mt-2">
                @if($problem->category)
                <span class="text-xs text-zinc-600 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $problem->category->color }}"></span>
                    {{ $problem->category->name }}
                </span>
                @endif
                <span class="text-xs text-zinc-600">{{ $problem->solutions_count }} solutions</span>
                <span class="text-xs text-zinc-600">{{ $problem->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <div class="flex flex-col items-end gap-1 text-xs text-zinc-500 shrink-0">
            <span class="font-medium text-zinc-400">{{ $problem->votes_count ?? 0 }}</span>
            <span>votes</span>
        </div>
    </a>
    @empty
    <div class="text-center py-16 rounded-xl border border-dashed border-zinc-800">
        <p class="text-sm text-zinc-500 mb-2">No problems found</p>
        <a href="{{ route('problems.create') }}" wire:navigate class="text-xs text-rose-400 hover:text-rose-300">Post the first one →</a>
    </div>
    @endforelse
</div>
