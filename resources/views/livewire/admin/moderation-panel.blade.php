<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-zinc-100">Admin — Moderation</h1>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-2xl font-bold text-zinc-100">{{ \App\Models\User::count() }}</div>
            <div class="text-xs text-zinc-500">Total Users</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-2xl font-bold text-zinc-100">{{ \App\Models\Problem::count() }}</div>
            <div class="text-xs text-zinc-500">Total Problems</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-2xl font-bold text-zinc-100">{{ \App\Models\Problem::where('status', 'open')->count() }}</div>
            <div class="text-xs text-zinc-500">Open Problems</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-2xl font-bold text-zinc-100">{{ \App\Models\Solution::count() }}</div>
            <div class="text-xs text-zinc-500">Total Solutions</div>
        </div>
    </div>

    <div class="p-6 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
        <h2 class="text-sm font-semibold text-zinc-300 mb-4">Recent Problems</h2>
        <div class="space-y-2">
            @forelse(\App\Models\Problem::latest()->limit(10)->get() as $problem)
            <div class="flex items-center gap-3 p-3 rounded-lg bg-zinc-800/50 border border-zinc-700/50">
                <span @class(['px-1.5 py-0.5 rounded text-[10px] font-semibold', $problem->status_color])>{{ ucfirst($problem->status) }}</span>
                <span class="flex-1 text-sm text-zinc-300 truncate">{{ $problem->title }}</span>
                <span class="text-xs text-zinc-600">{{ $problem->user->name }}</span>
            </div>
            @empty
            <p class="text-sm text-zinc-500 text-center py-4">No problems yet.</p>
            @endforelse
        </div>
    </div>
</div>
