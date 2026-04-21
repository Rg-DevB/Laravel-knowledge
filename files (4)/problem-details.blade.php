{{-- resources/views/livewire/problems/problem-details.blade.php --}}
<div class="flex gap-6">

    {{-- ── Main column ──────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-zinc-600 mb-5">
            <a href="{{ route('problems.index') }}" wire:navigate class="hover:text-zinc-400 transition-colors">Problems</a>
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            @if($problem->category)
            <a href="{{ route('problems.index', ['category' => $problem->category->slug]) }}" wire:navigate class="hover:text-zinc-400 transition-colors">{{ $problem->category->name }}</a>
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            @endif
            <span class="text-zinc-500 truncate max-w-xs">{{ Str::limit($problem->title, 40) }}</span>
        </nav>

        {{-- ── Problem header ──────────────────────────────── --}}
        <div class="mb-8">
            {{-- Status + Tags row --}}
            <div class="flex items-center flex-wrap gap-2 mb-4">
                <span @class([
                    'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold ring-1 ring-inset',
                    $problem->status_color
                ])>{{ ucfirst($problem->status) }}</span>

                @foreach($problem->tags as $tag)
                <span class="px-2 py-0.5 rounded-md text-xs font-mono"
                      style="background: {{ $tag->color }}18; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}30">
                    {{ $tag->name }}
                </span>
                @endforeach

                @if($problem->laravel_version)
                <span class="px-2 py-0.5 rounded-md text-xs font-mono bg-zinc-800 text-zinc-400 border border-zinc-700">
                    Laravel {{ $problem->laravel_version }}
                </span>
                @endif

                @if($problem->project_phase)
                <span class="px-2 py-0.5 rounded-md text-xs bg-zinc-800 text-zinc-500 border border-zinc-700">
                    Phase: {{ ucfirst($problem->project_phase) }}
                </span>
                @endif
            </div>

            <h1 class="text-xl font-bold text-zinc-100 leading-snug mb-4">{{ $problem->title }}</h1>

            {{-- Author + meta --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('profile.show', $problem->user->username) }}" wire:navigate class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                    <img src="{{ $problem->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($problem->user->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
                         class="w-6 h-6 rounded-full ring-1 ring-zinc-700" alt="{{ $problem->user->name }}">
                    <span class="text-sm text-zinc-400 hover:text-zinc-200 transition-colors">{{ $problem->user->name }}</span>
                </a>
                <span class="text-xs text-zinc-600">{{ $problem->created_at->diffForHumans() }}</span>
                <span class="text-xs text-zinc-600">{{ number_format($problem->views) }} views</span>
                <div class="flex items-center gap-1 text-xs text-zinc-600">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ $problem->solutions_count }} solution{{ $problem->solutions_count !== 1 ? 's' : '' }}
                </div>

                {{-- Actions --}}
                <div class="ml-auto flex items-center gap-2">
                    {{-- Favorite --}}
                    <button wire:click="toggleFavorite"
                            @class([
                                'flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-xs font-medium transition-all',
                                'border-yellow-500/40 bg-yellow-500/10 text-yellow-400' => $isFavorited,
                                'border-zinc-700 text-zinc-500 hover:border-zinc-600 hover:text-zinc-300' => !$isFavorited,
                            ])>
                        <svg class="w-3.5 h-3.5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                        {{ $isFavorited ? 'Saved' : 'Save' }}
                    </button>

                    {{-- Follow --}}
                    <button wire:click="toggleFollow"
                            @class([
                                'flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-xs font-medium transition-all',
                                'border-violet-500/40 bg-violet-500/10 text-violet-400' => $isFollowing,
                                'border-zinc-700 text-zinc-500 hover:border-zinc-600 hover:text-zinc-300' => !$isFollowing,
                            ])>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                        {{ $isFollowing ? 'Following' : 'Follow' }}
                    </button>

                    @can('update', $problem)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-1 px-2 py-1.5 rounded-lg border border-zinc-700 text-zinc-500 hover:text-zinc-300 hover:border-zinc-600 transition-all text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 top-8 w-36 bg-zinc-900 border border-zinc-700 rounded-lg shadow-xl overflow-hidden z-10">
                            @if($problem->status === 'open')
                            <button wire:click="closeProblem" @click="open = false" class="w-full text-left px-3 py-2 text-xs text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors">Close problem</button>
                            @else
                            <button wire:click="reopenProblem" @click="open = false" class="w-full text-left px-3 py-2 text-xs text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors">Reopen problem</button>
                            @endif
                        </div>
                    </div>
                    @endcan
                </div>
            </div>

            {{-- Description --}}
            <div class="prose prose-sm prose-invert max-w-none mb-6">
                {!! \League\CommonMark\CommonMarkConverter::class ? app(\League\CommonMark\MarkdownConverterInterface::class)->convert($problem->description) : nl2br(e($problem->description)) !!}
            </div>

            {{-- Package versions --}}
            @if($problem->package_versions)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($problem->package_versions as $pkg => $ver)
                <span class="px-2 py-0.5 rounded text-xs font-mono bg-zinc-800/80 text-zinc-400 border border-zinc-700">
                    {{ $pkg }}: {{ $ver }}
                </span>
                @endforeach
            </div>
            @endif

            {{-- Collapsible sections --}}
            @if($problem->error_log)
            <div class="mb-3">
                <button wire:click="$toggle('showErrorLog')" class="flex items-center gap-2 text-xs font-medium text-zinc-500 hover:text-zinc-300 transition-colors mb-2">
                    <svg class="w-3.5 h-3.5 transition-transform {{ $showErrorLog ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    Error Log / Stack Trace
                </button>
                @if($showErrorLog)
                <div class="rounded-xl border border-red-500/20 bg-red-500/5 overflow-hidden">
                    <div class="px-3 py-1.5 border-b border-red-500/10 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-xs text-red-400 font-medium">Error Output</span>
                    </div>
                    <pre class="p-4 text-xs font-mono text-zinc-300 overflow-x-auto leading-relaxed whitespace-pre-wrap">{{ $problem->error_log }}</pre>
                </div>
                @endif
            </div>
            @endif

            @if($problem->steps_to_reproduce)
            <div class="mb-3">
                <button wire:click="$toggle('showSteps')" class="flex items-center gap-2 text-xs font-medium text-zinc-500 hover:text-zinc-300 transition-colors mb-2">
                    <svg class="w-3.5 h-3.5 transition-transform {{ $showSteps ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    Steps to Reproduce
                </button>
                @if($showSteps)
                <div class="prose prose-sm prose-invert max-w-none p-4 rounded-xl bg-zinc-900/50 border border-zinc-800">
                    {!! nl2br(e($problem->steps_to_reproduce)) !!}
                </div>
                @endif
            </div>
            @endif

            @if($problem->expected_behavior || $problem->actual_behavior)
            <div class="grid sm:grid-cols-2 gap-3 mt-4">
                @if($problem->expected_behavior)
                <div class="p-3 rounded-xl bg-emerald-500/5 border border-emerald-500/20">
                    <p class="text-xs font-semibold text-emerald-400 mb-1.5">✓ Expected behavior</p>
                    <p class="text-xs text-zinc-400 leading-relaxed">{{ $problem->expected_behavior }}</p>
                </div>
                @endif
                @if($problem->actual_behavior)
                <div class="p-3 rounded-xl bg-red-500/5 border border-red-500/20">
                    <p class="text-xs font-semibold text-red-400 mb-1.5">✗ Actual behavior</p>
                    <p class="text-xs text-zinc-400 leading-relaxed">{{ $problem->actual_behavior }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Attachments --}}
            @if($problem->attachments->isNotEmpty())
            <div class="mt-4">
                <p class="text-xs font-medium text-zinc-500 mb-2">Attachments</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($problem->attachments as $attachment)
                    <a href="{{ Storage::url($attachment->path) }}" target="_blank"
                       class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-zinc-700 bg-zinc-800/50 text-xs text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                        {{ $attachment->filename }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ── Divider ──────────────────────────────────────── --}}
        <div class="border-t border-zinc-800/60 mb-8"></div>

        {{-- ── Solutions ────────────────────────────────────── --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold text-zinc-200">
                    {{ $this->solutions->count() }} Solution{{ $this->solutions->count() !== 1 ? 's' : '' }}
                </h2>
                @if($problem->status === 'solved')
                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 px-2.5 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Problem solved
                </span>
                @endif
            </div>

            @forelse($this->solutions as $solution)
            <div wire:key="solution-{{ $solution->id }}"
                 @class([
                     'flex gap-4 mb-5 p-5 rounded-xl border transition-all',
                     'border-emerald-500/30 bg-emerald-500/5 ring-1 ring-emerald-500/10' => $solution->is_best,
                     'border-zinc-800/60 bg-zinc-900/30' => !$solution->is_best,
                 ])>

                {{-- Vote widget --}}
                <div class="flex-shrink-0">
                    <livewire:voting.vote-system :model="$solution" modelType="solution" :key="'vote-'.$solution->id" />
                </div>

                {{-- Solution content --}}
                <div class="flex-1 min-w-0">

                    {{-- Header --}}
                    <div class="flex items-center flex-wrap gap-2 mb-3">
                        @if($solution->is_best)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gradient-to-r from-amber-500/20 to-orange-500/10 text-amber-400 border border-amber-500/25">
                            ★ Best Solution
                        </span>
                        @endif

                        <a href="{{ route('profile.show', $solution->user->username) }}" wire:navigate class="flex items-center gap-2 hover:opacity-80 transition-opacity ml-auto">
                            <img src="{{ $solution->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($solution->user->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
                                 class="w-5 h-5 rounded-full">
                            <span class="text-xs text-zinc-400">{{ $solution->user->name }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded bg-violet-500/15 text-violet-400 border border-violet-500/25">
                                {{ $solution->user->reputationBadge() }}
                            </span>
                        </a>
                        <span class="text-xs text-zinc-600">{{ $solution->created_at->diffForHumans() }}</span>

                        @can('update', $problem)
                        @if(!$solution->is_best && $problem->status !== 'closed')
                        <button wire:click="markBestSolution({{ $solution->id }})"
                                wire:confirm="Mark this as the best solution?"
                                class="text-xs px-2.5 py-1 rounded-lg border border-zinc-700 text-zinc-500 hover:border-emerald-500/40 hover:text-emerald-400 hover:bg-emerald-500/10 transition-all">
                            Mark as best
                        </button>
                        @endif
                        @endcan
                    </div>

                    {{-- Content --}}
                    <div class="prose prose-sm prose-invert max-w-none mb-4">
                        {!! app(\League\CommonMark\MarkdownConverterInterface::class)->convert($solution->content) !!}
                    </div>

                    {{-- Code snippets --}}
                    @if($solution->snippets->isNotEmpty())
                    <div class="mb-4">
                        <x-code-snippet :snippets="$solution->snippets" />
                    </div>
                    @endif

                    {{-- Comments on solution --}}
                    <div class="border-t border-zinc-800/60 pt-3 mt-3">
                        <livewire:comments.comment-thread :commentable="$solution" :key="'sc-'.$solution->id" />
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 rounded-xl border border-dashed border-zinc-800">
                <p class="text-sm text-zinc-500 mb-1">No solutions yet</p>
                <p class="text-xs text-zinc-600">Be the first to help solve this problem</p>
            </div>
            @endforelse
        </div>

        {{-- ── Add solution form ────────────────────────────── --}}
        @if($problem->status !== 'closed')
        <div class="border-t border-zinc-800/60 pt-8">
            <h2 class="text-base font-semibold text-zinc-200 mb-5">Your Solution</h2>
            @auth
            <livewire:solutions.solution-form :problem="$problem" />
            @else
            <div class="text-center py-10 rounded-xl bg-zinc-900/40 border border-zinc-800">
                <p class="text-sm text-zinc-400 mb-4">Sign in to post a solution</p>
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all">
                    Sign in
                </a>
            </div>
            @endauth
        </div>
        @endif

        {{-- ── Comments on problem ──────────────────────────── --}}
        <div class="border-t border-zinc-800/60 pt-8 mt-8">
            <h3 class="text-sm font-semibold text-zinc-400 mb-4">Discussion</h3>
            <livewire:comments.comment-thread :commentable="$problem" />
        </div>
    </div>

    {{-- ── Right sidebar ───────────────────────────────────────── --}}
    <aside class="hidden lg:block w-56 flex-shrink-0">
        <div class="sticky top-20 space-y-4">

            {{-- Problem meta --}}
            <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
                <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-3">Details</h3>
                <dl class="space-y-2">
                    @if($problem->laravel_version)
                    <div>
                        <dt class="text-xs text-zinc-600">Laravel</dt>
                        <dd class="text-xs text-zinc-300 font-mono">{{ $problem->laravel_version }}</dd>
                    </div>
                    @endif
                    @if($problem->project_phase)
                    <div>
                        <dt class="text-xs text-zinc-600">Phase</dt>
                        <dd class="text-xs text-zinc-300">{{ ucfirst($problem->project_phase) }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-zinc-600">Views</dt>
                        <dd class="text-xs text-zinc-300">{{ number_format($problem->views) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-zinc-600">Posted</dt>
                        <dd class="text-xs text-zinc-300">{{ $problem->created_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Related problems --}}
            @if($this->relatedProblems->isNotEmpty())
            <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
                <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-3">Related</h3>
                <div class="space-y-3">
                    @foreach($this->relatedProblems as $related)
                    <a href="{{ route('problems.show', $related->slug) }}" wire:navigate
                       class="block text-xs text-zinc-400 hover:text-zinc-200 transition-colors leading-snug">
                        {{ $related->title }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </aside>

</div>
