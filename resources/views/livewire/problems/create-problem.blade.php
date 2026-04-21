<div class="mb-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">Title</label>
            <input type="text" wire:model="title" placeholder="e.g. Livewire component not re-rendering after update..."
                   class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50">
            @error('title') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">Category</label>
            <select wire:model="categoryId"
                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50">
                <option value="">Select a category</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('categoryId') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-sm font-medium text-zinc-300 mb-2">Description</label>
        <textarea wire:model="description" rows="6" placeholder="Describe your problem in detail..."
                  class="w-full px-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50"></textarea>
        @error('description') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
    </div>

    @if($showSimilar && count($similarIssues) > 0)
    <div class="mt-4 p-4 rounded-xl bg-yellow-500/10 border border-yellow-500/20">
        <p class="text-xs font-medium text-yellow-400 mb-2">Similar issues found:</p>
        @foreach($similarIssues as $issue)
        <a href="{{ route('problems.show', $issue['slug']) }}" wire:navigate class="block text-xs text-zinc-400 hover:text-yellow-400 mb-1">
            → {{ $issue['title'] }} ({{ $issue['status'] }})
        </a>
        @endforeach
    </div>
    @endif
</div>

<button wire:click="save" class="px-6 py-3 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-medium text-sm transition-all">
    Post Problem
</button>
