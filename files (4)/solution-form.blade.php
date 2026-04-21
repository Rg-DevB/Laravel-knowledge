{{-- ============================================================
     resources/views/livewire/solutions/solution-form.blade.php
     ============================================================ --}}
<div>
    @if(session()->has('success'))
    <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Markdown editor --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Solution <span class="text-rose-400">*</span></label>
        <div x-data="{ tab: 'write' }" class="rounded-xl border border-zinc-700 overflow-hidden">
            {{-- Editor tabs --}}
            <div class="flex items-center gap-0 border-b border-zinc-700/60 bg-zinc-900/40">
                <button @click="tab = 'write'" :class="tab === 'write' ? 'border-b-2 border-rose-500 text-zinc-200' : 'text-zinc-500 hover:text-zinc-300'"
                        class="px-4 py-2 text-xs font-medium transition-all border-b-2 border-transparent">Write</button>
                <button @click="tab = 'preview'" :class="tab === 'preview' ? 'border-b-2 border-rose-500 text-zinc-200' : 'text-zinc-500 hover:text-zinc-300'"
                        class="px-4 py-2 text-xs font-medium transition-all border-b-2 border-transparent">Preview</button>
                <div class="ml-auto px-3 text-xs text-zinc-600">Supports **Markdown**</div>
            </div>
            {{-- Write --}}
            <div x-show="tab === 'write'">
                <textarea wire:model.lazy="content" rows="8"
                          class="w-full bg-zinc-900/60 px-4 py-3 text-sm text-zinc-100 placeholder-zinc-600 outline-none font-mono resize-none border-0"
                          placeholder="Explain your solution clearly…&#10;&#10;You can use Markdown for formatting:&#10;- **bold**, *italic*&#10;- `inline code`&#10;- ## headings"></textarea>
            </div>
            {{-- Preview --}}
            <div x-show="tab === 'preview'" x-cloak class="px-4 py-3 min-h-[200px]">
                <div class="prose prose-sm prose-invert max-w-none text-zinc-300">
                    @if($content)
                        {!! app(\League\CommonMark\MarkdownConverterInterface::class)->convert($content) !!}
                    @else
                        <p class="text-zinc-600 italic">Nothing to preview yet…</p>
                    @endif
                </div>
            </div>
        </div>
        @error('content') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Code snippets --}}
    @if(count($snippets) > 0)
    <div class="mb-4 space-y-3">
        <p class="text-sm font-medium text-zinc-300">Code Snippets</p>
        @foreach($snippets as $index => $snippet)
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="flex items-center gap-3 mb-3">
                <select wire:model="snippets.{{ $index }}.language"
                        class="bg-zinc-800 border border-zinc-700 rounded-lg px-2 py-1.5 text-xs text-zinc-300 outline-none">
                    @foreach(\App\Models\CodeSnippet::languages() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input wire:model="snippets.{{ $index }}.label" type="text" placeholder="Label (e.g. Before, After, Fix)"
                       class="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-2 py-1.5 text-xs text-zinc-300 placeholder-zinc-600 outline-none">
                <button wire:click="removeSnippet({{ $index }})" class="text-zinc-600 hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <textarea wire:model.lazy="snippets.{{ $index }}.code" rows="6"
                      class="w-full bg-[#0d0d14] border border-zinc-700/60 rounded-xl p-3 text-xs text-zinc-300 font-mono outline-none resize-none"
                      placeholder="Paste your code here…"></textarea>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button wire:click="addSnippet"
                class="flex items-center gap-2 px-3 py-2 rounded-xl border border-zinc-700 text-xs text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/></svg>
            Add Code Snippet
        </button>

        <button wire:click="save" wire:loading.attr="disabled"
                class="ml-auto flex items-center gap-2 px-5 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all shadow-lg shadow-rose-500/20 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">Post Solution</span>
            <span wire:loading wire:target="save">Posting…</span>
        </button>
    </div>
</div>
