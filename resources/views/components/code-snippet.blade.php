<div class="space-y-3">
    @php
    $languages = ['php' => 'PHP', 'blade' => 'Blade', 'livewire' => 'Livewire', 'javascript' => 'JavaScript', 'sql' => 'SQL', 'bash' => 'Bash', 'json' => 'JSON', 'yaml' => 'YAML', 'env' => '.env'];
    @endphp

    @foreach($snippets as $index => $snippet)
    <div class="rounded-xl border border-zinc-800/60 bg-zinc-900/50 overflow-hidden">
        @if($snippet->label || $snippet->language)
        <div class="flex items-center gap-2 px-4 py-2 border-b border-zinc-800/60 bg-zinc-800/30">
            @if($snippet->label)
            <span class="text-xs text-zinc-400">{{ $snippet->label }}</span>
            @endif
            <span class="text-xs text-zinc-600">{{ $languages[$snippet->language] ?? $snippet->language }}</span>
        </div>
        @endif
        <div class="relative">
            <button onclick="navigator.clipboard.writeText(`{{ addslashes($snippet->code) }}`)"
                    class="absolute top-2 right-2 p-1.5 rounded-lg bg-zinc-800/80 text-zinc-500 hover:text-zinc-300 hover:bg-zinc-700 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.64 1.763 1.54 1.914 2.654H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m-7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m-7.332 0c.646.049 1.288.11 1.927.184 1.1.64 1.763 1.54 1.914 2.654H6.75a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m-7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H3a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.64 1.763 1.54 1.914 2.654H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m-7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m-7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H3a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m-7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H2.25a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612"/></svg>
            </button>
            <pre class="p-4 text-xs font-mono text-zinc-300 overflow-x-auto"><code class="language-{{ $snippet->language }}">{{ $snippet->code }}</code></pre>
        </div>
    </div>
    @endforeach
</div>
