{{-- ============================================================
     resources/views/livewire/problems/problem-list.blade.php
     ============================================================ --}}
<div class="flex gap-6">

    {{-- ── Filters sidebar ──────────────────────────────────────── --}}
    <aside class="hidden xl:block w-56 flex-shrink-0">
        <div class="sticky top-20 space-y-6">

            {{-- Status filter --}}
            <div>
                <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Status</h3>
                <div class="space-y-1">
                    @foreach(['all' => 'All', 'open' => 'Open', 'solved' => 'Solved'] as $value => $label)
                    <button wire:click="$set('status', '{{ $value === 'all' ? '' : $value }}')"
                            @class([
                                'w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-sm transition-all text-left',
                                'bg-zinc-800 text-zinc-100' => ($status === ($value === 'all' ? '' : $value)),
                                'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/40' => ($status !== ($value === 'all' ? '' : $value)),
                            ])>
                        @if($value === 'open')   <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        @elseif($value === 'solved') <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        @else <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                        @endif
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Category filter --}}
            <div>
                <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Category</h3>
                <div class="space-y-1">
                    @foreach($this->categories as $cat)
                    <button wire:click="$set('category', '{{ $cat->slug }}')"
                            @class([
                                'w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-sm transition-all text-left',
                                'bg-zinc-800 text-zinc-100' => $category === $cat->slug,
                                'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/40' => $category !== $cat->slug,
                            ])>
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $cat->color }}"></span>
                        {{ $cat->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Laravel version filter --}}
            <div>
                <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Laravel Version</h3>
                <div class="space-y-1">
                    @foreach($this->laravelVersions as $version)
                    <button wire:click="$set('laravelVersion', '{{ $version }}')"
                            @class([
                                'w-full px-3 py-1.5 rounded-lg text-sm text-left transition-all',
                                'bg-zinc-800 text-zinc-100' => $laravelVersion === $version,
                                'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/40' => $laravelVersion !== $version,
                            ])>
                        {{ $version }}
                    </button>
                    @endforeach
                </div>
            </div>

            @if($status || $category || $laravelVersion || count($selectedTags))
            <button wire:click="clearFilters" class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                Clear all filters ✕
            </button>
            @endif

        </div>
    </aside>

    {{-- ── Main content ─────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0">

        {{-- Header row --}}
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-lg font-semibold text-zinc-100">Problems</h1>
                <p class="text-sm text-zinc-500 mt-0.5">
                    {{ $this->problems->total() }} issues found
                </p>
            </div>

            {{-- Sort tabs --}}
            <div class="flex items-center gap-1 p-1 rounded-lg bg-zinc-900 border border-zinc-800">
                @foreach(['recent' => 'Recent', 'popular' => 'Popular', 'unanswered' => 'Unanswered'] as $value => $label)
                <button wire:click="$set('sort', '{{ $value }}')"
                        @class([
                            'px-3 py-1.5 rounded-md text-xs font-medium transition-all',
                            'bg-zinc-700 text-zinc-100 shadow-sm' => $sort === $value,
                            'text-zinc-500 hover:text-zinc-300' => $sort !== $value,
                        ])>
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Problem list --}}
        <div class="space-y-2">
            @forelse($this->problems as $problem)
            <div wire:key="problem-{{ $problem->id }}"
                 class="group relative p-4 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 hover:border-zinc-700/60 transition-all duration-200">

                {{-- Status badge --}}
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <span @class(['inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium ring-1 ring-inset', $problem->status_color])>
                            {{ ucfirst($problem->status) }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
                           class="block text-sm font-medium text-zinc-200 hover:text-white transition-colors line-clamp-1 mb-1">
                            {{ $problem->title }}
                        </a>

                        <p class="text-xs text-zinc-500 line-clamp-2 mb-3">
                            {{ Str::limit(strip_tags($problem->description), 140) }}
                        </p>

                        {{-- Meta row --}}
                        <div class="flex items-center flex-wrap gap-x-4 gap-y-1.5">

                            {{-- Tags --}}
                            <div class="flex items-center gap-1.5">
                                @foreach($problem->tags->take(3) as $tag)
                                <button wire:click="toggleTag('{{ $tag->slug }}')"
                                        class="px-2 py-0.5 rounded-md text-xs font-mono transition-all"
                                        style="background: {{ $tag->color }}18; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}30">
                                    {{ $tag->name }}
                                </button>
                                @endforeach
                            </div>

                            {{-- Category --}}
                            @if($problem->category)
                            <span class="text-xs text-zinc-600">{{ $problem->category->name }}</span>
                            @endif

                            {{-- Laravel version --}}
                            @if($problem->laravel_version)
                            <span class="text-xs text-zinc-600 font-mono">L{{ $problem->laravel_version }}</span>
                            @endif

                            {{-- Spacer --}}
                            <div class="flex-1"></div>

                            {{-- Engagement stats --}}
                            <div class="flex items-center gap-3 text-xs text-zinc-600">
                                <span class="flex items-center gap-1" title="Votes">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" /></svg>
                                    {{ $problem->votes_count }}
                                </span>
                                <span class="flex items-center gap-1" title="Solutions">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    {{ $problem->solutions_count }}
                                </span>
                                <span class="flex items-center gap-1" title="Comments">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                                    {{ $problem->comments_count }}
                                </span>
                                {{-- Author --}}
                                <span class="flex items-center gap-1">
                                    <img src="{{ $problem->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($problem->user->name).'&size=16&background=1e1e2e&color=a78bfa' }}"
                                         class="w-3.5 h-3.5 rounded-full">
                                    {{ $problem->user->name }}
                                </span>
                                <span>{{ $problem->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Best solution indicator --}}
                @if($problem->status === 'solved')
                <div class="absolute right-3 top-3">
                    <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center" title="Has best solution">
                        <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                @endif

            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-zinc-400 mb-1">No problems found</h3>
                <p class="text-xs text-zinc-600 mb-5">Try adjusting your filters or be the first to post this issue.</p>
                <a href="{{ route('problems.create') }}" wire:navigate
                   class="px-4 py-2 rounded-lg bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all">
                    Post a problem
                </a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $this->problems->links('vendor.pagination.tailwind-dark') }}
        </div>
    </div>
</div>
