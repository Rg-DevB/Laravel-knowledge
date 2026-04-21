<div x-data="{ show: false }" class="relative">
    <button @click="show = !show" class="relative p-2 rounded-lg text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800 transition-all">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
        @if($unreadCount > 0)
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount }}</span>
        @endif
    </button>

    <div x-show="show" @click.away="show = false" x-cloak
         class="absolute right-0 top-full mt-2 w-80 bg-zinc-900 border border-zinc-700 rounded-xl shadow-2xl overflow-hidden z-50">
        <div class="px-4 py-3 border-b border-zinc-700 flex items-center justify-between">
            <span class="text-sm font-medium text-zinc-200">Notifications</span>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-xs text-rose-400 hover:text-rose-300">Mark all read</button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $notification)
            <a href="{{ $notification->data['url'] ?? '#' }}" wire:navigate
               class="block px-4 py-3 hover:bg-zinc-800 transition-colors border-b border-zinc-800/50 last:border-0 {{ is_null($notification->read_at) ? 'bg-zinc-800/30' : '' }}">
                <p class="text-xs text-zinc-300">{{ $notification->data['message'] ?? 'Notification' }}</p>
                <span class="text-xs text-zinc-600 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
            @empty
            <div class="px-4 py-8 text-center text-xs text-zinc-500">No notifications yet</div>
            @endforelse
        </div>
    </div>
</div>
