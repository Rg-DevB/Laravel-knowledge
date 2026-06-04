<div>
    {{-- ── Hero Section ────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-zinc-800/60 pb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-zinc-100 tracking-tight mb-2">
                Developer Tips
            </h1>
            <p class="text-sm text-zinc-400 max-w-xl">
                Practical, actionable advice to elevate your Laravel and PHP development skills. 
                Learn the "why" behind the code.
            </p>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-2xl font-bold text-white">{{ $this->readCount }}<span class="text-sm text-zinc-500 font-normal">/{{ $this->totalCount }}</span></div>
                <div class="text-[10px] text-zinc-500 uppercase tracking-widest font-semibold mt-0.5">Completed</div>
            </div>
            <a href="{{ route('tips.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-medium text-sm transition-all shadow-lg shadow-rose-500/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Share Tip
            </a>
        </div>
    </div>

    {{-- ── Filter & Search ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-6">
        <div class="relative w-full sm:w-96">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Recherche intelligente..."
                   class="w-full pl-9 pr-4 py-2 bg-zinc-900/40 border border-zinc-800/60 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 outline-none focus:border-rose-500/50 focus:ring-2 focus:ring-rose-500/20 transition-all">
        </div>

        <select wire:model.live="category" class="w-full sm:w-auto px-4 py-2 bg-zinc-900/40 border border-zinc-800/60 rounded-xl text-sm text-zinc-300 outline-none focus:border-rose-500/50 transition-all appearance-none cursor-pointer">
            <option value="all">All Categories</option>
            @foreach($this->categories as $key => $cat)
                <option value="{{ $key }}">{{ $cat['label'] }}</option>
            @endforeach
        </select>

        <select wire:model.live="difficulty" class="w-full sm:w-auto px-4 py-2 bg-zinc-900/40 border border-zinc-800/60 rounded-xl text-sm text-zinc-300 outline-none focus:border-rose-500/50 transition-all appearance-none cursor-pointer">
            <option value="all">All Levels</option>
            <option value="easy">Easy (Junior)</option>
            <option value="medium">Medium (Mid)</option>
            <option value="hard">Hard (Senior)</option>
        </select>
    </div>

    {{-- ── Tips List ─────────────────────────────────────────────── --}}
    <div class="space-y-3">
        @forelse($this->tips as $tip)
        @php
            $isRead = in_array($tip['id'], $readTips);
            $isOpen = $openTip === $tip['id'];
        @endphp

        <div wire:key="tip-{{ $tip['id'] }}" class="rounded-xl border border-zinc-800/60 bg-zinc-900/30 overflow-hidden transition-all duration-300">
            
            {{-- Header (Clickable) --}}
            <div wire:click="toggleTip({{ $tip['id'] }})" class="flex items-start gap-4 p-4 cursor-pointer hover:bg-zinc-900/60 transition-colors">
                
                {{-- Voting Column --}}
                <div class="flex flex-col items-center justify-center shrink-0" wire:click.stop>
                    <button wire:click="voteTip({{ $tip['id'] }}, 1)" class="p-1 rounded text-zinc-500 hover:text-emerald-400 hover:bg-emerald-500/10 {{ $tip['user_vote'] === 1 ? 'text-emerald-500' : '' }} transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <span class="text-xs font-bold {{ $tip['votes_count'] > 0 ? 'text-emerald-400' : ($tip['votes_count'] < 0 ? 'text-rose-400' : 'text-zinc-400') }}">{{ $tip['votes_count'] }}</span>
                    <button wire:click="voteTip({{ $tip['id'] }}, -1)" class="p-1 rounded text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 {{ $tip['user_vote'] === -1 ? 'text-rose-500' : '' }} transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <div class="flex-1 min-w-0 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-200 mb-1.5 line-clamp-1">{{ $tip['title'] }}</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-500/10 text-rose-400 ring-1 ring-rose-500/20 uppercase tracking-wide">
                            {{ $this->categories[$tip['category']]['label'] ?? $tip['category'] }}
                        </span>
                        <span class="text-xs text-zinc-500 font-medium capitalize">{{ $tip['difficulty'] }}</span>
                    </div>
                </div>

                <div class="shrink-0 flex items-center gap-3 pt-1">
                    <button wire:click.stop="toggleFavorite({{ $tip['id'] }})" class="p-1.5 rounded-lg text-zinc-500 hover:text-amber-400 hover:bg-amber-500/10 transition-colors {{ $tip['is_favorited'] ? 'text-amber-400' : '' }}">
                        <svg class="w-4 h-4 {{ $tip['is_favorited'] ? 'fill-current' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    </button>
                    @if($isRead)
                        <span class="hidden sm:inline text-[10px] uppercase font-bold tracking-widest text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded">Read</span>
                    @endif
                    <svg class="w-4 h-4 text-zinc-500 transition-transform duration-300 {{ $isOpen ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            {{-- Expanded Content --}}
            @if($isOpen)
            <div class="p-5 border-t border-zinc-800/60 bg-zinc-900/50">
                <div class="mb-6">
                    <h4 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">The Philosophy</h4>
                    <p class="text-sm text-zinc-300 leading-relaxed">{{ $tip['why'] }}</p>
                </div>

                <div class="grid md:grid-cols-2 gap-5 mb-6">
                    {{-- Junior Code --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-red-400 uppercase tracking-wider">Before / Avoid</span>
                            <a href="https://onlinephp.io/" target="_blank" rel="noreferrer" class="text-[10px] text-zinc-400 hover:text-white flex items-center gap-1 transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Playground
                            </a>
                        </div>
                        <div class="rounded-lg bg-zinc-950 border border-zinc-800/80 overflow-hidden relative group/code">
                            <div class="absolute top-2 right-2 opacity-0 group-hover/code:opacity-100 transition-opacity">
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText($el.closest('.relative').querySelector('code').innerText); copied = true; setTimeout(() => copied = false, 2000)" class="p-1.5 rounded-lg bg-zinc-800 text-zinc-400 hover:text-white transition-all">
                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-3 8.5h4m-4 1.5h4m-5 4h5" /></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </div>
                            <pre class="p-4 overflow-x-auto text-xs font-mono text-zinc-400 leading-relaxed"><code>{{ $tip['junior']['code'] }}</code></pre>
                            <div class="px-4 py-2 bg-red-500/5 border-t border-zinc-800/80 text-xs text-red-400/80">
                                {{ $tip['junior']['label'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Senior Code --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">After / Best Practice</span>
                            <a href="https://onlinephp.io/" target="_blank" rel="noreferrer" class="text-[10px] text-zinc-400 hover:text-white flex items-center gap-1 transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Playground
                            </a>
                        </div>
                        <div class="rounded-lg bg-zinc-950 border border-emerald-500/20 overflow-hidden relative group/code">
                            <div class="absolute top-2 right-2 opacity-0 group-hover/code:opacity-100 transition-opacity">
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText($el.closest('.relative').querySelector('code').innerText); copied = true; setTimeout(() => copied = false, 2000)" class="p-1.5 rounded-lg bg-zinc-800 text-zinc-400 hover:text-white transition-all">
                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-3 8.5h4m-4 1.5h4m-5 4h5" /></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </div>
                            <pre class="p-4 overflow-x-auto text-xs font-mono text-zinc-300 leading-relaxed"><code>{{ $tip['senior']['code'] }}</code></pre>
                            <div class="px-4 py-2 bg-emerald-500/5 border-t border-emerald-500/20 text-xs text-emerald-400">
                                {{ $tip['senior']['label'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-zinc-800/60 pt-4">
                    <button class="text-xs text-zinc-400 hover:text-white flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        {{ $tip['comments_count'] }} Comments
                    </button>
                    
                    <button wire:click="{{ $isRead ? 'markUnread('.$tip['id'].')' : 'markRead('.$tip['id'].')' }}"
                            @class([
                                'px-4 py-2 rounded-lg text-xs font-semibold transition-all',
                                'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' => $isRead,
                                'bg-zinc-800 text-zinc-300 hover:bg-zinc-700' => !$isRead,
                            ])>
                        {{ $isRead ? 'Mark as Unread' : 'Mark as Read' }}
                    </button>
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-16 rounded-xl border border-dashed border-zinc-800/80 bg-zinc-900/20">
            <p class="text-sm text-zinc-500 mb-2">No tips found matching your criteria</p>
            <button wire:click="$set('search', ''); $set('category', 'all'); $set('difficulty', 'all')" class="text-xs text-rose-400 hover:text-rose-300">
                Clear filters
            </button>
        </div>
        @endforelse
    </div>
</div>
