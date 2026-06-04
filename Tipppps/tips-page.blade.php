{{-- ============================================================
     resources/views/livewire/tips/tips-page.blade.php
     ============================================================ --}}
<div>

    {{-- ── Hero ──────────────────────────────────────────────────── --}}
    <div class="relative mb-8 p-7 rounded-2xl border border-zinc-800/60 bg-zinc-900/30 overflow-hidden">
        {{-- Glow effects --}}
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-rose-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 left-1/3 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-rose-500/25 bg-rose-500/10 text-xs text-rose-400 font-semibold mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                Niveau suivant
            </div>
            <h1 class="text-2xl font-bold text-zinc-100 mb-2 leading-tight">
                Tips
                <span class="bg-gradient-to-r from-rose-400 to-orange-400 bg-clip-text text-transparent">Junior → Senior</span>
            </h1>
            <p class="text-sm text-zinc-400 max-w-xl leading-relaxed mb-5">
                Chaque tip compare comment un développeur junior écrit le code… et comment un senior le pense. Pratique, concret, prêt à appliquer aujourd'hui.
            </p>
            <div class="flex flex-wrap gap-5">
                @foreach([
                    [$this->totalCount, 'Tips disponibles'],
                    [$this->readCount,  'Lus'],
                    [count($this->categories) - 1, 'Catégories'],
                ] as [$val, $lbl])
                <div>
                    <div class="text-xl font-bold text-zinc-100">{{ $val }}</div>
                    <div class="text-xs text-zinc-500 mt-0.5">{{ $lbl }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Progress bar ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-4 mb-7 p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
        </svg>
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium text-zinc-300">Ta progression</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-400 border border-amber-500/25">
                    {{ $this->progressBadge }}
                </span>
            </div>
            <div class="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-rose-500 to-amber-400 rounded-full transition-all duration-500"
                     style="width: {{ $this->progressPercent }}%"></div>
            </div>
        </div>
        <span class="text-xs text-zinc-500 flex-shrink-0 tabular-nums">{{ $this->readCount }} / {{ $this->totalCount }}</span>
    </div>

    {{-- ── Filters + Search ─────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">

        {{-- Search --}}
        <div class="relative flex-1 min-w-48 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input wire:model.live.debounce.200ms="search" type="text" placeholder="Chercher un tip…"
                   class="w-full pl-9 pr-3 py-2 bg-zinc-900/60 border border-zinc-700/60 focus:border-rose-500/50 focus:ring-1 focus:ring-rose-500/20 rounded-xl text-sm text-zinc-200 placeholder-zinc-600 outline-none transition-all">
        </div>

        {{-- Category filters --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach($this->categories as $key => $cat)
            <button wire:click="$set('category', '{{ $key }}')"
                    @class([
                        'px-3 py-1.5 rounded-full text-xs font-medium border transition-all',
                        'border-rose-500/40 bg-rose-500/10 text-rose-400'      => $category === $key && $key === 'all',
                        'border-orange-500/40 bg-orange-500/10 text-orange-400' => $category === $key && $key === 'eloquent',
                        'border-amber-500/40 bg-amber-500/10 text-amber-400'   => $category === $key && $key === 'performance',
                        'border-blue-500/40 bg-blue-500/10 text-blue-400'      => $category === $key && $key === 'security',
                        'border-violet-500/40 bg-violet-500/10 text-violet-400'=> $category === $key && $key === 'livewire',
                        'border-emerald-500/40 bg-emerald-500/10 text-emerald-400' => $category === $key && $key === 'architecture',
                        'border-red-500/40 bg-red-500/10 text-red-400'         => $category === $key && $key === 'blade',
                        'border-green-500/40 bg-green-500/10 text-green-400'   => $category === $key && $key === 'testing',
                        'border-zinc-700 text-zinc-500 hover:border-zinc-600 hover:text-zinc-300' => $category !== $key,
                    ])>
                {{ $cat['label'] }}
            </button>
            @endforeach
        </div>

        {{-- Difficulty filter --}}
        <select wire:model.live="difficulty"
                class="ml-auto bg-zinc-900/60 border border-zinc-700/60 focus:border-rose-500/50 rounded-xl px-3 py-2 text-xs text-zinc-400 outline-none transition-all">
            <option value="all">Tous niveaux</option>
            <option value="easy">Facile</option>
            <option value="medium">Moyen</option>
            <option value="hard">Avancé</option>
        </select>
    </div>

    {{-- ── Tips list ────────────────────────────────────────────── --}}
    <div class="space-y-3">
        @forelse($this->tips as $tip)

        @php
            $isRead = in_array($tip['id'], $readTips);
            $isOpen = $openTip === $tip['id'];
            $catColors = [
                'eloquent'     => 'text-orange-400 bg-orange-400/10 ring-orange-400/20',
                'security'     => 'text-blue-400 bg-blue-400/10 ring-blue-400/20',
                'performance'  => 'text-amber-400 bg-amber-400/10 ring-amber-400/20',
                'livewire'     => 'text-violet-400 bg-violet-400/10 ring-violet-400/20',
                'architecture' => 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20',
                'blade'        => 'text-red-400 bg-red-400/10 ring-red-400/20',
                'testing'      => 'text-green-400 bg-green-400/10 ring-green-400/20',
            ];
            $diffColors = [
                'easy'   => 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20',
                'medium' => 'text-amber-400 bg-amber-400/10 ring-amber-400/20',
                'hard'   => 'text-red-400 bg-red-400/10 ring-red-400/20',
            ];
            $diffLabels = ['easy' => 'Facile', 'medium' => 'Moyen', 'hard' => 'Avancé'];
        @endphp

        <div wire:key="tip-{{ $tip['id'] }}"
             @class([
                 'rounded-xl border overflow-hidden transition-all duration-200',
                 'border-emerald-500/20 bg-emerald-500/5' => $isRead,
                 'border-zinc-800/60 bg-zinc-900/30 hover:border-zinc-700/60' => !$isRead,
             ])>

            {{-- ── Tip header ──────────────────────────────── --}}
            <div wire:click="toggleTip({{ $tip['id'] }})"
                 class="flex items-center gap-3 px-5 py-3.5 cursor-pointer select-none">

                {{-- Number --}}
                <div @class([
                    'w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0 font-mono transition-all',
                    'bg-emerald-500/15 text-emerald-400' => $isRead,
                    'bg-zinc-800 text-zinc-500' => !$isRead,
                ])>
                    @if($isRead) ✓ @else {{ str_pad($tip['id'], 2, '0', STR_PAD_LEFT) }} @endif
                </div>

                {{-- Category badge --}}
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold ring-1 ring-inset {{ $catColors[$tip['category']] ?? '' }}">
                    {{ $this->categories[$tip['category']]['label'] ?? $tip['category'] }}
                </span>

                {{-- Title --}}
                <span class="flex-1 text-sm font-medium text-zinc-200 truncate">{{ $tip['title'] }}</span>

                {{-- Difficulty --}}
                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium ring-1 ring-inset {{ $diffColors[$tip['difficulty']] ?? '' }}">
                    {{ $diffLabels[$tip['difficulty']] ?? '' }}
                </span>

                {{-- Mark read button --}}
                <button
                    wire:click.stop="{{ $isRead ? 'markUnread('.$tip['id'].')' : 'markRead('.$tip['id'].')' }}"
                    @class([
                        'flex-shrink-0 text-xs px-2.5 py-1 rounded-lg border transition-all',
                        'border-emerald-500/30 bg-emerald-500/10 text-emerald-400' => $isRead,
                        'border-zinc-700 text-zinc-600 hover:border-emerald-500/30 hover:text-emerald-400 hover:bg-emerald-500/10' => !$isRead,
                    ])>
                    {{ $isRead ? '✓ Lu' : 'Marquer lu' }}
                </button>

                {{-- Toggle arrow --}}
                <svg class="w-4 h-4 text-zinc-500 flex-shrink-0 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : '' }}"
                     fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </div>

            {{-- ── Tip body ─────────────────────────────────── --}}
            @if($isOpen)
            <div class="px-5 pb-5 border-t border-zinc-800/40">

                {{-- Why it matters --}}
                <div class="mt-4 mb-5 flex items-start gap-3 p-3.5 rounded-xl bg-amber-500/5 border border-amber-500/20">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    <p class="text-xs text-zinc-300 leading-relaxed">
                        <strong class="text-amber-400">Pourquoi ça compte : </strong>{{ $tip['why'] }}
                    </p>
                </div>

                {{-- Code comparison --}}
                <div class="grid sm:grid-cols-2 gap-3">

                    {{-- Junior card --}}
                    <div class="rounded-xl border border-red-500/20 bg-red-500/5 overflow-hidden">
                        <div class="flex items-center gap-2.5 px-4 py-2.5 border-b border-red-500/15 bg-red-500/8">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide bg-red-500/15 text-red-400 border border-red-500/25">Junior</span>
                            <span class="text-xs text-zinc-400 flex-1 truncate">{{ $tip['junior']['label'] }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-500/15 text-orange-400 border border-orange-500/20 font-mono uppercase">{{ strtoupper($tip['junior']['lang']) }}</span>
                            <button
                                x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText($el.closest('.rounded-xl').querySelector('pre').innerText).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                class="text-[10px] px-2 py-0.5 rounded border border-zinc-700 text-zinc-500 hover:text-zinc-300 hover:border-zinc-600 transition-all bg-zinc-900/60"
                                x-text="copied ? '✓ Copié' : 'Copy'"></button>
                        </div>
                        <pre class="p-4 text-xs font-mono leading-relaxed overflow-x-auto text-zinc-300">{{ $tip['junior']['code'] }}</pre>
                    </div>

                    {{-- Senior card --}}
                    <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 overflow-hidden">
                        <div class="flex items-center gap-2.5 px-4 py-2.5 border-b border-emerald-500/15 bg-emerald-500/8">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">Senior</span>
                            <span class="text-xs text-zinc-400 flex-1 truncate">{{ $tip['senior']['label'] }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-500/15 text-orange-400 border border-orange-500/20 font-mono uppercase">{{ strtoupper($tip['senior']['lang']) }}</span>
                            <button
                                x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText($el.closest('.rounded-xl').querySelector('pre').innerText).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                class="text-[10px] px-2 py-0.5 rounded border border-zinc-700 text-zinc-500 hover:text-zinc-300 hover:border-zinc-600 transition-all bg-zinc-900/60"
                                x-text="copied ? '✓ Copié' : 'Copy'"></button>
                        </div>
                        <pre class="p-4 text-xs font-mono leading-relaxed overflow-x-auto text-zinc-300">{{ $tip['senior']['code'] }}</pre>
                    </div>
                </div>

                {{-- Pros/Cons --}}
                @if(!empty($tip['pros']))
                <div class="grid sm:grid-cols-2 gap-2 mt-3">
                    @foreach($tip['pros'] as $pro)
                    <div @class([
                        'flex items-start gap-2 p-2.5 rounded-lg border text-xs text-zinc-400',
                        'border-red-500/15 bg-red-500/5'       => $pro['type'] === 'bad',
                        'border-emerald-500/15 bg-emerald-500/5' => $pro['type'] === 'good',
                    ])>
                        <span>{{ $pro['type'] === 'bad' ? '❌' : '✅' }}</span>
                        {{ $pro['text'] }}
                    </div>
                    @endforeach
                </div>
                @endif

            </div>
            @endif

        </div>
        @empty
        <div class="text-center py-20 rounded-xl border border-dashed border-zinc-800">
            <p class="text-sm text-zinc-500 mb-1">Aucun tip trouvé pour ces filtres.</p>
            <button wire:click="$set('category','all'); $set('search','')" class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                Réinitialiser les filtres
            </button>
        </div>
        @endforelse
    </div>

    {{-- ── Contribute CTA ───────────────────────────────────────── --}}
    <div class="mt-10 p-5 rounded-xl border border-dashed border-zinc-700/60 bg-zinc-900/20 text-center">
        <p class="text-sm font-medium text-zinc-300 mb-1">Tu as un tip à partager ?</p>
        <p class="text-xs text-zinc-500 mb-4">Propose un tip Junior → Senior et aide la communauté à progresser.</p>
        <a href="{{ route('tips.create') }}" wire:navigate
           class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all shadow-lg shadow-rose-500/20">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Proposer un tip
        </a>
    </div>

</div>
