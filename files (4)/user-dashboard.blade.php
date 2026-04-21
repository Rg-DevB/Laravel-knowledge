{{-- resources/views/livewire/dashboard/user-dashboard.blade.php --}}
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div class="flex items-center gap-4">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1e1e2e&color=a78bfa&size=64' }}"
                 class="w-14 h-14 rounded-2xl ring-2 ring-zinc-800" alt="{{ auth()->user()->name }}">
            <div>
                <h1 class="text-lg font-bold text-zinc-100">{{ auth()->user()->name }}</h1>
                <p class="text-sm text-zinc-500">@{{ auth()->user()->username }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs px-2 py-0.5 rounded-lg bg-violet-500/15 text-violet-400 border border-violet-500/25 font-medium">
                        {{ $this->stats['badge'] }}
                    </span>
                    <span class="text-xs text-zinc-500">{{ number_format($this->stats['reputation']) }} reputation</span>
                </div>
            </div>
        </div>
        <a href="{{ route('settings') }}" wire:navigate
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-700 text-xs text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
            Settings
        </a>
    </div>

    {{-- Reputation progress --}}
    @if($this->stats['next_badge_at']['threshold'])
    <div class="mb-8 p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-zinc-400">Progress to <strong class="text-violet-400">{{ $this->stats['next_badge_at']['badge'] }}</strong></span>
            <span class="text-xs font-mono text-zinc-500">{{ number_format($this->stats['reputation']) }} / {{ number_format($this->stats['next_badge_at']['threshold']) }}</span>
        </div>
        <div class="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-violet-500 to-rose-500 rounded-full transition-all duration-500"
                 style="width: {{ $this->stats['next_badge_at']['progress'] }}%"></div>
        </div>
    </div>
    @endif

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
        @foreach([
            ['label' => 'Problems Posted',   'value' => $this->stats['problems_posted'],  'icon' => '📝', 'color' => 'text-zinc-200'],
            ['label' => 'Solutions Posted',   'value' => $this->stats['solutions_posted'], 'icon' => '✅', 'color' => 'text-emerald-400'],
            ['label' => 'Best Solutions',     'value' => $this->stats['best_solutions'],   'icon' => '⭐', 'color' => 'text-amber-400'],
            ['label' => 'Upvotes Received',   'value' => $this->stats['total_upvotes'],    'icon' => '👍', 'color' => 'text-rose-400'],
        ] as $stat)
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-xl mb-1">{{ $stat['icon'] }}</div>
            <div class="text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 p-1 bg-zinc-900 border border-zinc-800 rounded-xl mb-6 w-fit">
        @foreach(['overview' => 'Overview', 'problems' => 'My Problems', 'solutions' => 'My Solutions', 'activity' => 'Activity'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                @class([
                    'px-3 py-1.5 rounded-lg text-xs font-medium transition-all',
                    'bg-zinc-700 text-zinc-100' => $activeTab === $tab,
                    'text-zinc-500 hover:text-zinc-300' => $activeTab !== $tab,
                ])>{{ $label }}</button>
        @endforeach
    </div>

    {{-- Tab: Overview --}}
    @if($activeTab === 'overview')
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Recent problems --}}
        <div>
            <h3 class="text-sm font-semibold text-zinc-400 mb-3">Recent Problems</h3>
            <div class="space-y-2">
                @forelse($this->myProblems->take(5) as $problem)
                <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
                   class="flex items-center gap-3 p-3 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all group">
                    <span @class(['px-1.5 py-0.5 rounded text-[10px] font-semibold', $problem->status_color])>{{ ucfirst($problem->status) }}</span>
                    <span class="flex-1 text-xs text-zinc-300 group-hover:text-white transition-colors truncate">{{ $problem->title }}</span>
                    <span class="text-xs text-zinc-600">{{ $problem->solutions_count }} solutions</span>
                </a>
                @empty
                <p class="text-xs text-zinc-600 text-center py-6">No problems yet. <a href="{{ route('problems.create') }}" wire:navigate class="text-rose-400">Post one →</a></p>
                @endforelse
            </div>
        </div>

        {{-- Recent activity --}}
        <div>
            <h3 class="text-sm font-semibold text-zinc-400 mb-3">Recent Activity</h3>
            <div class="space-y-1">
                @forelse($this->recentActivity->take(8) as $log)
                <div class="flex items-center gap-3 py-2 border-b border-zinc-800/40">
                    <span @class([
                        'text-xs font-semibold px-2 py-0.5 rounded-md min-w-[3rem] text-center',
                        'bg-emerald-500/15 text-emerald-400' => $log->points > 0,
                        'bg-red-500/15 text-red-400' => $log->points < 0,
                    ])>{{ $log->points > 0 ? '+' : '' }}{{ $log->points }}</span>
                    <span class="flex-1 text-xs text-zinc-400">{{ \App\Models\ReputationLog::reasonLabel($log->reason) }}</span>
                    <span class="text-xs text-zinc-600 flex-shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-xs text-zinc-600 text-center py-6">No activity yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- Tab: My Problems --}}
    @if($activeTab === 'problems')
    <div class="space-y-2">
        @forelse($this->myProblems as $problem)
        <div class="flex items-start gap-4 p-4 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all">
            <span @class(['mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold', $problem->status_color])>{{ ucfirst($problem->status) }}</span>
            <div class="flex-1 min-w-0">
                <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
                   class="text-sm font-medium text-zinc-200 hover:text-white transition-colors line-clamp-1">{{ $problem->title }}</a>
                <div class="flex items-center gap-3 mt-1 text-xs text-zinc-600">
                    @if($problem->category) <span>{{ $problem->category->name }}</span> @endif
                    <span>{{ $problem->solutions_count }} solutions</span>
                    <span>{{ $problem->comments_count }} comments</span>
                    <span>{{ $problem->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <p class="text-sm text-zinc-500 mb-4">You haven't posted any problems yet.</p>
            <a href="{{ route('problems.create') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all">Post a Problem</a>
        </div>
        @endforelse
        <div class="mt-4">{{ $this->myProblems->links() }}</div>
    </div>
    @endif

    {{-- Tab: My Solutions --}}
    @if($activeTab === 'solutions')
    <div class="space-y-2">
        @forelse($this->mySolutions as $solution)
        <a href="{{ route('problems.show', $solution->problem->slug) }}" wire:navigate
           class="flex items-start gap-3 p-4 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all">
            @if($solution->is_best)
            <span class="shrink-0 mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-400/10 text-amber-400 border border-amber-400/20">★ Best</span>
            @else
            <span class="shrink-0 mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-zinc-800 text-zinc-500 border border-zinc-700">Solution</span>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-sm text-zinc-300 hover:text-white transition-colors line-clamp-1 font-medium">{{ $solution->problem->title }}</p>
                <p class="text-xs text-zinc-500 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($solution->content), 100) }}</p>
                <div class="flex items-center gap-3 mt-1 text-xs text-zinc-600">
                    <span>+{{ $solution->votes_count }} votes</span>
                    <span>{{ $solution->comments_count }} comments</span>
                    <span>{{ $solution->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-16">
            <p class="text-sm text-zinc-500">You haven't posted any solutions yet.</p>
        </div>
        @endforelse
        <div class="mt-4">{{ $this->mySolutions->links() }}</div>
    </div>
    @endif

    {{-- Tab: Activity --}}
    @if($activeTab === 'activity')
    <div class="space-y-1">
        @forelse($this->recentActivity as $log)
        <div class="flex items-center gap-4 py-3 border-b border-zinc-800/40">
            <span @class([
                'text-sm font-bold px-2.5 py-1 rounded-lg min-w-[3.5rem] text-center',
                'bg-emerald-500/15 text-emerald-400' => $log->points > 0,
                'bg-red-500/15 text-red-400' => $log->points < 0,
            ])>{{ $log->points > 0 ? '+' : '' }}{{ $log->points }}</span>
            <div class="flex-1">
                <p class="text-sm text-zinc-300">{{ \App\Models\ReputationLog::reasonLabel($log->reason) }}</p>
            </div>
            <span class="text-xs text-zinc-600 flex-shrink-0">{{ $log->created_at->diffForHumans() }}</span>
        </div>
        @empty
        <p class="text-center py-10 text-sm text-zinc-600">No activity yet.</p>
        @endforelse
    </div>
    @endif

</div>
