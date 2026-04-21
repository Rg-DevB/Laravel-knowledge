{{-- resources/views/livewire/home/landing-page.blade.php --}}
<div>
    {{-- ── Hero ──────────────────────────────────────────────────── --}}
    <section class="relative py-16 overflow-hidden">
        {{-- Background glow --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-rose-500/5 blur-3xl rounded-full"></div>
        </div>

        <div class="max-w-3xl mx-auto text-center px-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-zinc-700/60 bg-zinc-900/60 text-xs text-zinc-400 mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ number_format($this->stats['problems']) }} problems documented · {{ number_format($this->stats['solved']) }} solved
            </div>

            <h1 class="text-4xl sm:text-5xl font-bold text-zinc-100 leading-[1.15] mb-5 tracking-tight">
                The Laravel knowledge base<br>
                <span class="bg-gradient-to-r from-rose-400 to-orange-400 bg-clip-text text-transparent">built by developers</span>
            </h1>

            <p class="text-base text-zinc-400 max-w-xl mx-auto mb-10 leading-relaxed">
                Document your Laravel problems, share solutions, and help the community. From Eloquent quirks to Livewire bugs — find answers fast.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('problems.create') }}" wire:navigate
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-medium text-sm transition-all shadow-xl shadow-rose-500/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Post a Problem
                </a>
                <a href="{{ route('problems.index') }}" wire:navigate
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-zinc-700 hover:border-zinc-600 text-zinc-300 hover:text-white font-medium text-sm transition-all">
                    Browse Problems
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ── Stats row ─────────────────────────────────────────────── --}}
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-12">
        @foreach([
            ['label' => 'Problems posted',  'value' => $this->stats['problems'],  'color' => 'text-rose-400'],
            ['label' => 'Solutions shared',  'value' => $this->stats['solutions'], 'color' => 'text-blue-400'],
            ['label' => 'Members joined',    'value' => $this->stats['members'],   'color' => 'text-violet-400'],
            ['label' => 'Problems solved',   'value' => $this->stats['solved'],    'color' => 'text-emerald-400'],
        ] as $stat)
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60 text-center">
            <div class="text-2xl font-bold {{ $stat['color'] }} mb-1">{{ number_format($stat['value']) }}</div>
            <div class="text-xs text-zinc-500">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </section>

    {{-- ── Categories grid ───────────────────────────────────────── --}}
    <section class="mb-12">
        <h2 class="text-sm font-semibold text-zinc-500 uppercase tracking-wider mb-4">Browse by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
            @foreach($this->categories as $cat)
            <a href="{{ route('problems.index', ['category' => $cat->slug]) }}" wire:navigate
               class="flex items-center gap-3 p-3 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 hover:border-zinc-700/60 transition-all group">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $cat->color }}"></span>
                <span class="text-sm text-zinc-400 group-hover:text-zinc-200 transition-colors flex-1 truncate">{{ $cat->name }}</span>
                <span class="text-xs text-zinc-600">{{ $cat->problems_count }}</span>
            </a>
            @endforeach
        </div>
    </section>

    {{-- ── Recent problems + top contributors (2 col) ────────────── --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Recent problems --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider">Recent Problems</h2>
                <a href="{{ route('problems.index') }}" wire:navigate class="text-xs text-rose-400 hover:text-rose-300 transition-colors">View all →</a>
            </div>
            <div class="space-y-2">
                @foreach($this->recentProblems as $problem)
                <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
                   class="flex items-start gap-3 p-3.5 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 hover:border-zinc-700/60 transition-all group">

                    <span @class([
                        'mt-0.5 shrink-0 inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold',
                        'bg-blue-400/10 text-blue-400 ring-1 ring-blue-400/20'    => $problem->status === 'open',
                        'bg-emerald-400/10 text-emerald-400 ring-1 ring-emerald-400/20' => $problem->status === 'solved',
                    ])>{{ ucfirst($problem->status) }}</span>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-zinc-300 group-hover:text-white transition-colors truncate">{{ $problem->title }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            @if($problem->category)
                            <span class="text-xs text-zinc-600">{{ $problem->category->name }}</span>
                            @endif
                            <span class="text-xs text-zinc-600">{{ $problem->solutions_count }} solution{{ $problem->solutions_count !== 1 ? 's' : '' }}</span>
                            <span class="text-xs text-zinc-600">{{ $problem->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">

            {{-- Top contributors --}}
            <div>
                <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Top Contributors</h2>
                <div class="space-y-2">
                    @foreach($this->topContributors as $i => $user)
                    <a href="{{ route('profile.show', $user->username) }}" wire:navigate
                       class="flex items-center gap-3 p-3 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all">
                        <span class="text-xs text-zinc-600 w-4 text-center font-mono">{{ $i + 1 }}</span>
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
                             class="w-7 h-7 rounded-full" alt="{{ $user->name }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-zinc-300 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-zinc-600">{{ $user->reputationBadge() }}</p>
                        </div>
                        <span class="text-xs font-semibold text-violet-400">{{ number_format($user->reputation) }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Popular tags --}}
            <div>
                <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Popular Tags</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->popularTags as $tag)
                    <a href="{{ route('problems.index', ['tags' => [$tag->slug]]) }}" wire:navigate
                       class="px-2.5 py-1 rounded-lg text-xs font-mono transition-all hover:opacity-80"
                       style="background: {{ $tag->color }}18; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}30">
                        {{ $tag->name }}
                        <span class="opacity-60 ml-1">{{ $tag->usage_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
