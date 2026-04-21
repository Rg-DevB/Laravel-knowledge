<?php
// ============================================================
// app/Livewire/Profile/UserProfile.php
// ============================================================
namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;

class UserProfile extends Component
{
    public User $user;
    public string $activeTab = 'problems';

    public function mount(string $username): void
    {
        $this->user = User::where('username', $username)->firstOrFail();
    }

    #[Computed]
    public function problems()
    {
        return $this->user->problems()
            ->with(['category', 'tags'])
            ->withCount('solutions')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function solutions()
    {
        return $this->user->solutions()
            ->with('problem.category')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'problems'      => $this->user->problems()->count(),
            'solutions'     => $this->user->solutions()->count(),
            'best'          => $this->user->solutions()->where('is_best', true)->count(),
            'reputation'    => $this->user->reputation,
        ];
    }

    public function render()
    {
        return view('livewire.profile.user-profile')
            ->layout('layouts.app', ['title' => $this->user->name . ' — LaravelKnow']);
    }
}


{{-- ============================================================
     resources/views/livewire/profile/user-profile.blade.php
     ============================================================ --}}
<div class="max-w-3xl mx-auto">

    {{-- Profile header --}}
    <div class="flex items-start gap-6 mb-8">
        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1e1e2e&color=a78bfa&size=80' }}"
             class="w-20 h-20 rounded-2xl ring-2 ring-zinc-800 flex-shrink-0" alt="{{ $user->name }}">
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-zinc-100">{{ $user->name }}</h1>
                    <p class="text-sm text-zinc-500">@{{ $user->username }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-500/15 text-violet-400 border border-violet-500/25">
                        {{ $user->reputationBadge() }}
                    </span>
                    <span class="text-sm font-bold text-zinc-300">{{ number_format($user->reputation) }} <span class="text-xs font-normal text-zinc-500">rep</span></span>
                </div>
            </div>

            @if($user->bio)
            <p class="text-sm text-zinc-400 mt-3 leading-relaxed">{{ $user->bio }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-4 mt-3">
                @if($user->github_url)
                <a href="{{ $user->github_url }}" target="_blank" class="flex items-center gap-1.5 text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    GitHub
                </a>
                @endif
                @if($user->twitter_url)
                <a href="{{ $user->twitter_url }}" target="_blank" class="flex items-center gap-1.5 text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    X / Twitter
                </a>
                @endif
                @if($user->website_url)
                <a href="{{ $user->website_url }}" target="_blank" class="flex items-center gap-1.5 text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3 12a8.96 8.96 0 0 0 .284 2.253"/></svg>
                    Website
                </a>
                @endif
                <span class="text-xs text-zinc-600">Joined {{ $user->created_at->format('M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-4 gap-3 mb-8">
        @foreach([
            ['value' => $this->stats['problems'],   'label' => 'Problems'],
            ['value' => $this->stats['solutions'],   'label' => 'Solutions'],
            ['value' => $this->stats['best'],        'label' => 'Best solutions'],
            ['value' => $this->stats['reputation'],  'label' => 'Reputation'],
        ] as $stat)
        <div class="text-center p-3 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-xl font-bold text-zinc-200 mb-0.5">{{ number_format($stat['value']) }}</div>
            <div class="text-xs text-zinc-500">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 p-1 bg-zinc-900 border border-zinc-800 rounded-xl w-fit">
        @foreach(['problems' => 'Problems', 'solutions' => 'Solutions'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                @class([
                    'px-4 py-1.5 rounded-lg text-xs font-medium transition-all',
                    'bg-zinc-700 text-zinc-100' => $activeTab === $tab,
                    'text-zinc-500 hover:text-zinc-300' => $activeTab !== $tab,
                ])>{{ $label }}</button>
        @endforeach
    </div>

    {{-- Problems tab --}}
    @if($activeTab === 'problems')
    <div class="space-y-2">
        @forelse($this->problems as $problem)
        <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
           class="flex items-start gap-3 p-4 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all group">
            <span @class(['mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold', $problem->status_color])>{{ ucfirst($problem->status) }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-zinc-300 group-hover:text-white transition-colors truncate">{{ $problem->title }}</p>
                <div class="flex items-center gap-3 mt-1 text-xs text-zinc-600">
                    @if($problem->category) <span>{{ $problem->category->name }}</span> @endif
                    <span>{{ $problem->solutions_count }} solutions</span>
                    <span>{{ $problem->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @empty
        <p class="text-center py-10 text-sm text-zinc-600">No problems posted yet.</p>
        @endforelse
        {{ $this->problems->links() }}
    </div>
    @endif

    {{-- Solutions tab --}}
    @if($activeTab === 'solutions')
    <div class="space-y-2">
        @forelse($this->solutions as $solution)
        <a href="{{ route('problems.show', $solution->problem->slug) }}" wire:navigate
           class="flex items-start gap-3 p-4 rounded-xl border border-zinc-800/60 bg-zinc-900/30 hover:bg-zinc-900/60 transition-all group">
            @if($solution->is_best)
            <span class="shrink-0 mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-400/10 text-amber-400">★</span>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-sm text-zinc-300 group-hover:text-white transition-colors truncate">{{ $solution->problem->title }}</p>
                <p class="text-xs text-zinc-500 truncate mt-0.5">{{ Str::limit(strip_tags($solution->content), 100) }}</p>
                <div class="flex items-center gap-3 mt-1 text-xs text-zinc-600">
                    <span>+{{ $solution->votes_count }} votes</span>
                    <span>{{ $solution->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @empty
        <p class="text-center py-10 text-sm text-zinc-600">No solutions posted yet.</p>
        @endforelse
        {{ $this->solutions->links() }}
    </div>
    @endif

</div>
