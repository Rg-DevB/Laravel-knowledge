{{-- resources/views/livewire/problems/create-problem.blade.php --}}
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-xl font-bold text-zinc-100 mb-1">Post a Problem</h1>
        <p class="text-sm text-zinc-500">Document your Laravel issue so the community can help you solve it.</p>
    </div>

    {{-- Step progress --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1 => 'Basics', 2 => 'Context', 3 => 'Details'] as $n => $label)
        <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <div @class([
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold transition-all',
                'bg-rose-500 text-white' => $step === $n,
                'bg-emerald-500 text-white' => $step > $n,
                'bg-zinc-800 text-zinc-500 border border-zinc-700' => $step < $n,
            ])>
                @if($step > $n)
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                @else
                {{ $n }}
                @endif
            </div>
            <span @class([
                'text-xs font-medium',
                'text-zinc-100' => $step === $n,
                'text-zinc-400' => $step !== $n,
            ])>{{ $label }}</span>
            @if(!$loop->last)
            <div class="flex-1 h-px bg-zinc-800 mx-1"></div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ── Similar issues suggestion ──────────────────────────── --}}
    @if($showSimilar)
    <div x-data class="mb-6 p-4 rounded-xl bg-amber-500/8 border border-amber-500/25">
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-amber-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            <div class="flex-1">
                <p class="text-xs font-semibold text-amber-400 mb-2">Similar issues found — check before posting:</p>
                <div class="space-y-1.5">
                    @foreach($similarIssues as $issue)
                    <a href="{{ route('problems.show', $issue['slug']) }}" target="_blank"
                       class="flex items-center gap-2 text-xs text-zinc-300 hover:text-white transition-colors">
                        <span @class([
                            'shrink-0 px-1.5 py-0.5 rounded text-[10px] font-semibold',
                            'bg-emerald-400/10 text-emerald-400' => $issue['status'] === 'solved',
                            'bg-blue-400/10 text-blue-400' => $issue['status'] === 'open',
                        ])>{{ ucfirst($issue['status']) }}</span>
                        {{ $issue['title'] }}
                        <svg class="w-3 h-3 text-zinc-600 ml-auto flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    </a>
                    @endforeach
                </div>
                <p class="text-xs text-zinc-500 mt-2">Still not the same issue? Continue posting below.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── STEP 1: Basics ─────────────────────────────────────── --}}
    @if($step === 1)
    <div class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Problem Title <span class="text-rose-400">*</span></label>
            <input wire:model.live.debounce.300ms="title" type="text"
                   class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 transition-all outline-none"
                   placeholder="e.g. Livewire 3 wire:navigate breaks browser back button">
            @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-zinc-600">Be specific. Good titles get faster answers.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Description <span class="text-rose-400">*</span></label>
            <textarea wire:model.lazy="description" rows="6"
                      class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 transition-all outline-none font-mono resize-none"
                      placeholder="Describe your problem in detail. Supports **Markdown**.&#10;&#10;Include what you've already tried..."></textarea>
            @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Category <span class="text-rose-400">*</span></label>
            <select wire:model="categoryId"
                    class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 transition-all outline-none">
                <option value="">Select a category…</option>
                @foreach($this->categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('categoryId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Tags <span class="text-zinc-500 text-xs font-normal">(max 5)</span></label>
            <div x-data="{ input: '' }" class="space-y-2">
                {{-- Selected tags --}}
                @if(count($tags))
                <div class="flex flex-wrap gap-1.5">
                    @foreach($tags as $tag)
                    <span class="flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-mono"
                          style="background: {{ $tag['color'] }}18; color: {{ $tag['color'] }}; border: 1px solid {{ $tag['color'] }}30">
                        {{ $tag['name'] }}
                        <button wire:click="removeTag({{ $tag['id'] }})" class="hover:opacity-60 transition-opacity">✕</button>
                    </span>
                    @endforeach
                </div>
                @endif
                {{-- Tag input --}}
                @if(count($tags) < 5)
                <div class="flex items-center gap-2">
                    <input x-model="input" @keydown.enter.prevent="if(input.trim()) { $wire.addTag(input.trim()); input = ''; }"
                           type="text" placeholder="Add tag (press Enter)…"
                           class="flex-1 bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 transition-all outline-none">
                    <button @click="if(input.trim()) { $wire.addTag(input.trim()); input = ''; }"
                            class="px-3 py-2 rounded-xl bg-zinc-800 border border-zinc-700 text-xs text-zinc-400 hover:text-zinc-200 transition-all">
                        Add
                    </button>
                </div>
                {{-- Popular tags suggestion --}}
                <div class="flex flex-wrap gap-1.5">
                    @foreach($this->popularTags->take(8) as $popularTag)
                    @if(!in_array($popularTag->id, array_column($tags, 'id')))
                    <button wire:click="addTag('{{ $popularTag->name }}')"
                            class="px-2 py-0.5 rounded-md text-xs font-mono opacity-60 hover:opacity-100 transition-opacity"
                            style="background: {{ $popularTag->color }}18; color: {{ $popularTag->color }}; border: 1px solid {{ $popularTag->color }}30">
                        + {{ $popularTag->name }}
                    </button>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── STEP 2: Laravel Context ─────────────────────────────── --}}
    @if($step === 2)
    <div class="space-y-5">
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Laravel Version</label>
                <select wire:model="laravelVersion"
                        class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 transition-all outline-none">
                    <option value="">Select version…</option>
                    @foreach(['12.x','11.x','10.x','9.x','8.x','7.x'] as $v)
                    <option>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Project Phase</label>
                <select wire:model="projectPhase"
                        class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 transition-all outline-none">
                    <option value="">Select phase…</option>
                    @foreach(['setup','authentication','database','api','frontend','queues','deployment','production','testing'] as $phase)
                    <option value="{{ $phase }}">{{ ucfirst($phase) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Package Versions <span class="text-zinc-500 text-xs font-normal">(optional)</span></label>
            <p class="text-xs text-zinc-600 mb-2">Specify versions of relevant packages (Livewire, Sanctum, etc.)</p>
            <div class="space-y-2">
                @foreach(['livewire/livewire', 'laravel/sanctum', 'laravel/horizon', 'spatie/laravel-permission'] as $pkg)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono text-zinc-500 w-48 truncate">{{ $pkg }}</span>
                    <input wire:model="packageVersions.{{ str_replace(['/','.'], ['_','_'], $pkg) }}"
                           type="text" placeholder="e.g. 3.x"
                           class="flex-1 bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 rounded-xl px-3 py-1.5 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all font-mono">
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── STEP 3: Details ────────────────────────────────────── --}}
    @if($step === 3)
    <div class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Error Log / Stack Trace <span class="text-zinc-500 text-xs font-normal">(optional)</span></label>
            <textarea wire:model.lazy="errorLog" rows="5"
                      class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-xs text-zinc-300 placeholder-zinc-600 transition-all outline-none font-mono resize-none"
                      placeholder="Paste your full error message or stack trace here…"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-1.5">Steps to Reproduce <span class="text-zinc-500 text-xs font-normal">(optional)</span></label>
            <textarea wire:model.lazy="stepsToReproduce" rows="4"
                      class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 transition-all outline-none resize-none"
                      placeholder="1. Run php artisan migrate&#10;2. Visit /dashboard&#10;3. Click on..."></textarea>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Expected Behavior</label>
                <textarea wire:model.lazy="expectedBehavior" rows="3"
                          class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-emerald-500/60 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 transition-all outline-none resize-none"
                          placeholder="What should happen…"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Actual Behavior</label>
                <textarea wire:model.lazy="actualBehavior" rows="3"
                          class="w-full bg-zinc-900/60 border border-zinc-700 focus:border-red-500/60 focus:ring-1 focus:ring-red-500/30 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 transition-all outline-none resize-none"
                          placeholder="What actually happens…"></textarea>
            </div>
        </div>
    </div>
    @endif

    {{-- Navigation buttons --}}
    <div class="flex items-center justify-between mt-8 pt-6 border-t border-zinc-800/60">
        @if($step > 1)
        <button wire:click="$set('step', {{ $step - 1 }})"
                class="flex items-center gap-2 px-4 py-2 rounded-xl border border-zinc-700 text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 text-sm transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back
        </button>
        @else
        <div></div>
        @endif

        @if($step < 3)
        <button wire:click="nextStep"
                class="flex items-center gap-2 px-5 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-200 text-sm font-medium transition-all">
            Continue
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </button>
        @else
        <button wire:click="save" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-6 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all shadow-lg shadow-rose-500/20 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">Post Problem</span>
            <span wire:loading wire:target="save">Posting…</span>
            <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
        </button>
        @endif
    </div>
</div>
