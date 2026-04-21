<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-zinc-100">Profile</h1>
    </div>

    <div class="flex items-center gap-4 mb-8">
        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1e1e2e&color=a78bfa&size=64' }}"
             class="w-16 h-16 rounded-2xl ring-2 ring-zinc-800" alt="{{ $user->name }}">
        <div>
            <h2 class="text-lg font-bold text-zinc-100">{{ $user->name }}</h2>
            <p class="text-sm text-zinc-500">@{{ $user->username }}</p>
            @if($user->bio)
            <p class="text-xs text-zinc-400 mt-1">{{ $user->bio }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-8">
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60 text-center">
            <div class="text-xl font-bold text-zinc-100">{{ number_format($user->reputation) }}</div>
            <div class="text-xs text-zinc-500">Reputation</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60 text-center">
            <div class="text-xl font-bold text-zinc-100">{{ $user->problems()->count() }}</div>
            <div class="text-xs text-zinc-500">Problems</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60 text-center">
            <div class="text-xl font-bold text-zinc-100">{{ $user->solutions()->count() }}</div>
            <div class="text-xs text-zinc-500">Solutions</div>
        </div>
    </div>

    <div class="flex items-center gap-1 p-1 bg-zinc-900 border border-zinc-800 rounded-xl mb-6 w-fit">
        <button wire:click="$set('activeTab', 'problems')"
                @class(['px-3 py-1.5 rounded-lg text-xs font-medium transition-all', 'bg-zinc-700 text-zinc-100' => $activeTab === 'problems', 'text-zinc-500 hover:text-zinc-300' => $activeTab !== 'problems'])>
            Problems
        </button>
        <button wire:click="$set('activeTab', 'solutions')"
                @class(['px-3 py-1.5 rounded-lg text-xs font-medium transition-all', 'bg-zinc-700 text-zinc-100' => $activeTab === 'solutions', 'text-zinc-500 hover:text-zinc-300' => $activeTab !== 'solutions'])>
            Solutions
        </button>
    </div>

    @if($activeTab === 'problems')
    <div class="space-y-2">
        @forelse($user->problems()->latest()->paginate(10) as $problem)
        <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
           class="flex items-center gap-3 p-3 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all">
            <span @class(['px-1.5 py-0.5 rounded text-[10px] font-semibold', $problem->status_color])>{{ ucfirst($problem->status) }}</span>
            <span class="flex-1 text-sm text-zinc-300 truncate">{{ $problem->title }}</span>
            <span class="text-xs text-zinc-600">{{ $problem->solutions_count }} solutions</span>
        </a>
        @empty
        <p class="text-sm text-zinc-500 text-center py-8">No problems posted yet.</p>
        @endforelse
    </div>
    @endif

    @if($activeTab === 'solutions')
    <div class="space-y-2">
        @forelse($user->solutions()->latest()->paginate(10) as $solution)
        <a href="{{ route('problems.show', $solution->problem->slug) }}" wire:navigate
           class="flex items-center gap-3 p-3 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all">
            @if($solution->is_best)
            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-400/10 text-amber-400">★ Best</span>
            @endif
            <span class="flex-1 text-sm text-zinc-300 truncate">{{ $solution->problem->title }}</span>
            <span class="text-xs text-zinc-600">+{{ $solution->votes_count ?? 0 }} votes</span>
        </a>
        @empty
        <p class="text-sm text-zinc-500 text-center py-8">No solutions posted yet.</p>
        @endforelse
    </div>
    @endif
</div>
