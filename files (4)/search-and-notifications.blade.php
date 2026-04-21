{{-- ============================================================
     resources/views/livewire/search/search-bar.blade.php
     ============================================================ --}}
<div class="relative w-full" x-data @click.outside="$wire.showSuggestions = false">
    <div class="relative flex items-center">
        <svg class="absolute left-3 w-3.5 h-3.5 text-zinc-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
        </svg>
        <input wire:model.live.debounce.300ms="query"
               wire:keydown.enter="search"
               @keydown.escape="$wire.showSuggestions = false"
               type="text"
               placeholder="Search Laravel problems…"
               class="w-full pl-9 pr-16 py-2 bg-zinc-900/60 border border-zinc-700/60 hover:border-zinc-600 focus:border-rose-500/50 focus:ring-1 focus:ring-rose-500/20 rounded-xl text-sm text-zinc-200 placeholder-zinc-600 outline-none transition-all">
        <kbd class="absolute right-3 px-1.5 py-0.5 rounded text-[10px] font-mono text-zinc-600 bg-zinc-800 border border-zinc-700">⌘K</kbd>
    </div>

    {{-- Suggestions dropdown --}}
    @if($showSuggestions && count($suggestions))
    <div class="absolute top-full left-0 right-0 mt-1.5 bg-zinc-900 border border-zinc-700/80 rounded-xl shadow-2xl shadow-black/50 overflow-hidden z-50">
        <div class="px-3 py-2 border-b border-zinc-800/60">
            <p class="text-xs text-zinc-500">Similar issues found:</p>
        </div>
        <div>
            @foreach($suggestions as $suggestion)
            <a href="{{ route('problems.show', $suggestion['slug']) }}" wire:navigate
               class="flex items-center gap-3 px-4 py-2.5 hover:bg-zinc-800/60 transition-colors group">
                <span @class([
                    'flex-shrink-0 px-1.5 py-0.5 rounded text-[10px] font-semibold',
                    'bg-emerald-400/10 text-emerald-400' => $suggestion['status'] === 'solved',
                    'bg-blue-400/10 text-blue-400' => $suggestion['status'] !== 'solved',
                ])>{{ ucfirst($suggestion['status']) }}</span>
                <span class="flex-1 text-sm text-zinc-300 group-hover:text-white transition-colors truncate">{{ $suggestion['title'] }}</span>
                <span class="flex-shrink-0 text-xs text-zinc-600">+{{ $suggestion['votes_count'] }}</span>
            </a>
            @endforeach
        </div>
        <div class="px-4 py-2 border-t border-zinc-800/60">
            <button wire:click="search" class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                Search all results for "{{ $query }}" →
            </button>
        </div>
    </div>
    @endif
</div>


{{-- ============================================================
     app/Livewire/Notifications/NotificationBell.php
     ============================================================ --}}
<?php
namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\Computed;

class NotificationBell extends Component
{
    public bool $open = false;

    #[Computed]
    public function notifications()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->find($id)?->markAsRead();
    }

    public function toggleOpen(): void
    {
        $this->open = !$this->open;
    }

    public function render()
    {
        return view('livewire.notifications.notification-bell');
    }
}


{{-- ============================================================
     resources/views/livewire/notifications/notification-bell.blade.php
     ============================================================ --}}
<div class="relative" x-data @click.outside="$wire.open = false">
    <button wire:click="toggleOpen"
            class="relative flex items-center justify-center w-8 h-8 rounded-lg border border-zinc-700/60 hover:border-zinc-600 bg-zinc-900/40 text-zinc-500 hover:text-zinc-300 transition-all">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
        </svg>
        @if($this->unreadCount > 0)
        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-[9px] font-bold text-white flex items-center justify-center">
            {{ min($this->unreadCount, 9) }}
        </span>
        @endif
    </button>

    @if($open)
    <div class="absolute right-0 top-10 w-80 bg-zinc-900 border border-zinc-700/80 rounded-xl shadow-2xl shadow-black/50 overflow-hidden z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800/60">
            <span class="text-sm font-semibold text-zinc-200">Notifications</span>
            @if($this->unreadCount > 0)
            <button wire:click="markAllRead" class="text-xs text-zinc-500 hover:text-zinc-300 transition-colors">Mark all read</button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($this->notifications as $notification)
            @php $data = $notification->data; @endphp
            <div wire:click="markRead('{{ $notification->id }}')"
                 @class([
                     'flex items-start gap-3 px-4 py-3 hover:bg-zinc-800/40 transition-colors cursor-pointer border-b border-zinc-800/30',
                     'bg-rose-500/5' => is_null($notification->read_at),
                 ])>
                {{-- Dot for unread --}}
                <div class="mt-1.5 flex-shrink-0">
                    @if(is_null($notification->read_at))
                    <div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div>
                    @else
                    <div class="w-1.5 h-1.5"></div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-zinc-300 leading-snug mb-1">{{ $data['message'] ?? 'New notification' }}</p>
                    <p class="text-xs text-zinc-600">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <svg class="w-6 h-6 text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                <p class="text-xs text-zinc-600">No notifications yet</p>
            </div>
            @endforelse
        </div>

        <div class="px-4 py-2.5 border-t border-zinc-800/60">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-xs text-zinc-500 hover:text-zinc-300 transition-colors">View all activity →</a>
        </div>
    </div>
    @endif
</div>
