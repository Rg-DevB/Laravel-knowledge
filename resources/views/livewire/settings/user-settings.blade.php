<div>
    <h1 class="text-xl font-bold text-zinc-100 mb-6">Settings</h1>

    <div class="max-w-xl space-y-6">
        <div class="p-6 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <h2 class="text-sm font-semibold text-zinc-300 mb-4">Profile Information</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Name</label>
                    <input type="text" wire:model="name"
                           class="w-full px-4 py-2.5 rounded-xl bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50">
                </div>

                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Username</label>
                    <input type="text" wire:model="username"
                           class="w-full px-4 py-2.5 rounded-xl bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50">
                </div>

                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Email</label>
                    <input type="email" wire:model="email"
                           class="w-full px-4 py-2.5 rounded-xl bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50">
                </div>

                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Bio</label>
                    <textarea wire:model="bio" rows="3"
                             class="w-full px-4 py-2.5 rounded-xl bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"></textarea>
                </div>

                <div>
                    <label class="block text-xs text-zinc-500 mb-1">GitHub URL</label>
                    <input type="url" wire:model="github_url"
                           class="w-full px-4 py-2.5 rounded-xl bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50">
                </div>

                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Website URL</label>
                    <input type="url" wire:model="website_url"
                           class="w-full px-4 py-2.5 rounded-xl bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50">
                </div>
            </div>

            <button wire:click="save"
                    class="mt-6 px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-medium text-sm transition-all">
                Save Changes
            </button>
        </div>
    </div>
</div>
