{{-- ============================================================
     resources/views/components/code-snippet.blade.php
     GitHub-quality code blocks with tabs, copy, expand
     Usage: <x-code-snippet :snippets="$solution->snippets" />
     ============================================================ --}}
@props(['snippets'])

@if($snippets->isNotEmpty())
<div
    x-data="{
        activeTab: 0,
        expanded: {},
        copied: null,

        copyCode(index) {
            const pre = this.$refs['code-' + index];
            navigator.clipboard.writeText(pre.innerText).then(() => {
                this.copied = index;
                setTimeout(() => this.copied = null, 2000);
            });
        },

        toggleExpand(index) {
            this.expanded[index] = !this.expanded[index];
        },

        isExpanded(index) {
            return this.expanded[index] ?? false;
        }
    }"
    class="rounded-xl overflow-hidden border border-zinc-700/60 bg-[#0d0d14] font-mono text-sm">

    {{-- ── Tab bar ──────────────────────────────────────────── --}}
    <div class="flex items-center gap-0 border-b border-zinc-700/60 bg-zinc-900/60 overflow-x-auto">
        @foreach($snippets as $index => $snippet)
        <button
            @click="activeTab = {{ $index }}"
            :class="activeTab === {{ $index }}
                ? 'border-b-2 border-rose-500 text-zinc-200 bg-[#0d0d14]'
                : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/30'"
            class="flex items-center gap-2 px-4 py-2.5 text-xs whitespace-nowrap transition-all border-b-2 border-transparent">

            {{-- Language icon / badge --}}
            <span @class([
                'px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                'bg-orange-500/20 text-orange-400' => $snippet->language === 'php',
                'bg-red-500/20 text-red-400'       => $snippet->language === 'blade',
                'bg-violet-500/20 text-violet-400' => $snippet->language === 'livewire',
                'bg-yellow-500/20 text-yellow-400' => $snippet->language === 'javascript',
                'bg-blue-500/20 text-blue-400'     => $snippet->language === 'sql',
                'bg-green-500/20 text-green-400'   => $snippet->language === 'bash',
                'bg-zinc-500/20 text-zinc-400'     => !in_array($snippet->language, ['php','blade','livewire','javascript','sql','bash']),
            ])>
                {{ strtoupper($snippet->language) }}
            </span>

            {{ $snippet->label ?: \App\Models\CodeSnippet::languages()[$snippet->language] ?? $snippet->language }}
        </button>
        @endforeach
    </div>

    {{-- ── Code panels ──────────────────────────────────────── --}}
    @foreach($snippets as $index => $snippet)
    <div x-show="activeTab === {{ $index }}" x-cloak>

        {{-- Toolbar --}}
        <div class="flex items-center justify-between px-4 py-2 border-b border-zinc-800/60 bg-zinc-900/30">
            <div class="flex items-center gap-2">
                {{-- Traffic light dots --}}
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
            </div>

            <div class="flex items-center gap-2">

                {{-- Expand / Collapse --}}
                <button
                    @click="toggleExpand({{ $index }})"
                    class="flex items-center gap-1.5 px-2 py-1 rounded text-zinc-600 hover:text-zinc-400 hover:bg-zinc-800 transition-all text-xs">
                    <svg x-show="!isExpanded({{ $index }})" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                    </svg>
                    <svg x-show="isExpanded({{ $index }})" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                    </svg>
                    <span x-text="isExpanded({{ $index }}) ? 'Collapse' : 'Expand'" class="hidden sm:inline"></span>
                </button>

                {{-- Copy button --}}
                <button
                    @click="copyCode({{ $index }})"
                    class="flex items-center gap-1.5 px-2.5 py-1 rounded text-xs transition-all"
                    :class="copied === {{ $index }}
                        ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'
                        : 'bg-zinc-800 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 border border-zinc-700'">
                    <svg x-show="copied !== {{ $index }}" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                    </svg>
                    <svg x-show="copied === {{ $index }}" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span x-text="copied === {{ $index }} ? 'Copied!' : 'Copy'"></span>
                </button>

            </div>
        </div>

        {{-- Code block --}}
        <div :class="isExpanded({{ $index }}) ? '' : 'max-h-[400px]'"
             class="overflow-auto transition-all duration-300">
            <pre x-ref="code-{{ $index }}"
                 class="p-5 text-sm leading-relaxed overflow-x-auto"><code class="language-{{ $snippet->language }} hljs">{{ $snippet->code }}</code></pre>
        </div>

        {{-- Fade mask when collapsed --}}
        <div x-show="!isExpanded({{ $index }})" x-cloak
             class="h-10 -mt-10 relative bg-gradient-to-t from-[#0d0d14] to-transparent pointer-events-none">
        </div>

    </div>
    @endforeach

</div>
@endif
