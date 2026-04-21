<div>
    <textarea wire:model="content" rows="6" placeholder="Write your solution... (Markdown supported)"
              class="w-full px-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50"></textarea>
    @error('content') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror

    <div class="mt-4">
        <button type="button" wire:click="addSnippet"
                class="text-xs text-zinc-500 hover:text-zinc-300 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add code snippet
        </button>
    </div>

    @foreach($snippets as $index => $snippet)
    <div class="mt-4 p-4 rounded-xl bg-zinc-800/50 border border-zinc-700/50">
        <div class="flex gap-3 mb-3">
            <select wire:model="snippets.{{ $index }}.language" class="text-xs bg-zinc-800 border border-zinc-600 rounded-lg px-2 py-1 text-zinc-300">
                <option value="php">PHP</option>
                <option value="blade">Blade</option>
                <option value="livewire">Livewire</option>
                <option value="javascript">JavaScript</option>
                <option value="sql">SQL</option>
                <option value="bash">Bash</option>
                <option value="json">JSON</option>
                <option value="yaml">YAML</option>
            </select>
            <input type="text" wire:model="snippets.{{ $index }}.label" placeholder="Label (optional)"
                   class="flex-1 text-xs bg-zinc-800 border border-zinc-600 rounded-lg px-2 py-1 text-zinc-300">
            <button type="button" wire:click="removeSnippet({{ $index }})" class="text-zinc-600 hover:text-red-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <textarea wire:model="snippets.{{ $index }}.code" rows="4" placeholder="Paste code here..."
                  class="w-full px-3 py-2 rounded-lg bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 font-mono text-xs focus:outline-none focus:ring-2 focus:ring-rose-500/50"></textarea>
    </div>
    @endforeach

    <button type="button" wire:click="save"
            class="mt-6 px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-medium text-sm transition-all">
        Post Solution
    </button>
</div>
