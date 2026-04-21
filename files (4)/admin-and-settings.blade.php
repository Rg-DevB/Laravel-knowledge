{{-- ============================================================
     resources/views/livewire/admin/moderation-panel.blade.php
     ============================================================ --}}
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-xl font-bold text-zinc-100">Moderation Panel</h1>
            <p class="text-sm text-zinc-500">Manage content, users and edit suggestions.</p>
        </div>
        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-500/15 text-red-400 border border-red-500/25">Admin</span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
        @foreach([
            ['label' => 'Total Problems',    'value' => $this->stats['total_problems'],      'color' => 'text-zinc-200'],
            ['label' => 'Open Problems',     'value' => $this->stats['open_problems'],       'color' => 'text-blue-400'],
            ['label' => 'Total Users',       'value' => $this->stats['total_users'],         'color' => 'text-violet-400'],
            ['label' => 'Pending Edits',     'value' => $this->stats['pending_suggestions'], 'color' => 'text-amber-400'],
        ] as $stat)
        <div class="p-4 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
            <div class="text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 p-1 bg-zinc-900 border border-zinc-800 rounded-xl mb-6 w-fit">
        @foreach(['overview' => 'Overview', 'problems' => 'Problems', 'users' => 'Users', 'suggestions' => 'Edit Suggestions'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                @class([
                    'px-3 py-1.5 rounded-lg text-xs font-medium transition-all',
                    'bg-zinc-700 text-zinc-100' => $activeTab === $tab,
                    'text-zinc-500 hover:text-zinc-300' => $activeTab !== $tab,
                ])>{{ $label }}</button>
        @endforeach
    </div>

    {{-- Problems tab --}}
    @if($activeTab === 'problems')
    <div class="rounded-xl border border-zinc-800/60 overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-zinc-800/60 bg-zinc-900/40">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Problem</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Author</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/40">
                @foreach($this->recentProblems as $problem)
                <tr class="hover:bg-zinc-800/20 transition-colors">
                    <td class="px-4 py-3">
                        <a href="{{ route('problems.show', $problem->slug) }}" wire:navigate
                           class="text-sm text-zinc-300 hover:text-white transition-colors line-clamp-1">{{ $problem->title }}</a>
                    </td>
                    <td class="px-4 py-3 text-xs text-zinc-500">{{ $problem->user->name }}</td>
                    <td class="px-4 py-3">
                        <span @class(['px-2 py-0.5 rounded text-[10px] font-semibold', $problem->status_color])>{{ ucfirst($problem->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-zinc-600">{{ $problem->created_at->format('M j, Y') }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="deleteProblem({{ $problem->id }})"
                                wire:confirm="Delete this problem?"
                                class="text-xs text-red-500 hover:text-red-400 transition-colors">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-800/40">{{ $this->recentProblems->links() }}</div>
    </div>
    @endif

    {{-- Users tab --}}
    @if($activeTab === 'users')
    <div class="rounded-xl border border-zinc-800/60 overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-zinc-800/60 bg-zinc-900/40">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">User</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Reputation</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Joined</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/40">
                @foreach($this->recentUsers as $user)
                <tr class="hover:bg-zinc-800/20 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
                                 class="w-6 h-6 rounded-full" alt="{{ $user->name }}">
                            <div>
                                <p class="text-sm text-zinc-300">{{ $user->name }}</p>
                                <p class="text-xs text-zinc-600">@{{ $user->username }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span @class([
                            'px-2 py-0.5 rounded text-[10px] font-semibold',
                            'bg-red-500/15 text-red-400 border border-red-500/20' => $user->role === 'admin',
                            'bg-amber-500/15 text-amber-400 border border-amber-500/20' => $user->role === 'moderator',
                            'bg-zinc-800 text-zinc-500 border border-zinc-700' => $user->role === 'user',
                        ])>{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-zinc-400">{{ number_format($user->reputation) }}</td>
                    <td class="px-4 py-3 text-xs text-zinc-600">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="px-4 py-3">
                        @if($user->id !== auth()->id())
                        <button wire:click="banUser({{ $user->id }})"
                                wire:confirm="Ban this user?"
                                class="text-xs text-red-500 hover:text-red-400 transition-colors">Ban</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-800/40">{{ $this->recentUsers->links() }}</div>
    </div>
    @endif

    {{-- Edit suggestions tab --}}
    @if($activeTab === 'suggestions')
    <div class="space-y-3">
        @forelse($this->pendingSuggestions as $suggestion)
        <div class="p-4 rounded-xl border border-amber-500/20 bg-amber-500/5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-amber-400">Pending Edit</span>
                    <span class="text-xs text-zinc-500">by {{ $suggestion->user->name }}</span>
                    <span class="text-xs text-zinc-600">{{ $suggestion->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="approveSuggestion({{ $suggestion->id }})"
                            class="px-3 py-1 rounded-lg text-xs font-medium bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 hover:bg-emerald-500/25 transition-all">
                        Approve
                    </button>
                    <button wire:click="rejectSuggestion({{ $suggestion->id }})"
                            class="px-3 py-1 rounded-lg text-xs font-medium bg-zinc-800 text-zinc-400 border border-zinc-700 hover:bg-zinc-700 transition-all">
                        Reject
                    </button>
                </div>
            </div>
            @if($suggestion->reason)
            <p class="text-xs text-zinc-500 mb-3 italic">"{{ $suggestion->reason }}"</p>
            @endif
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <p class="text-xs font-semibold text-zinc-500 mb-1">Original</p>
                    <div class="p-3 rounded-lg bg-zinc-900/60 border border-zinc-700 text-xs text-zinc-400 font-mono max-h-32 overflow-y-auto">{{ $suggestion->original_content }}</div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-emerald-400 mb-1">Suggested</p>
                    <div class="p-3 rounded-lg bg-emerald-500/5 border border-emerald-500/20 text-xs text-zinc-300 font-mono max-h-32 overflow-y-auto">{{ $suggestion->suggested_content }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <p class="text-sm text-zinc-500">No pending edit suggestions.</p>
        </div>
        @endforelse
    </div>
    @endif
</div>


{{-- ============================================================
     resources/views/livewire/settings/user-settings.blade.php
     ============================================================ --}}
<div class="max-w-xl mx-auto">

    <h1 class="text-xl font-bold text-zinc-100 mb-8">Settings</h1>

    {{-- Profile section --}}
    <div class="mb-8 p-6 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
        <h2 class="text-sm font-semibold text-zinc-300 mb-5">Profile Information</h2>

        @if($saved)
        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            ✓ Profile updated successfully.
        </div>
        @endif

        <div class="space-y-4">
            {{-- Avatar upload --}}
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Avatar</label>
                <div class="flex items-center gap-4">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1e1e2e&color=a78bfa&size=64' }}"
                         class="w-14 h-14 rounded-xl ring-1 ring-zinc-700" alt="avatar">
                    <div>
                        <input type="file" wire:model="avatar" accept="image/*" class="text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700">
                        <p class="text-xs text-zinc-600 mt-1">JPG, PNG, GIF — max 2MB</p>
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">Full Name</label>
                    <input wire:model.defer="name" type="text"
                           class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/20 rounded-xl px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all">
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-sm text-zinc-600">@</span>
                        <input wire:model.defer="username" type="text"
                               class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/20 rounded-xl pl-7 pr-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all">
                    </div>
                    @error('username') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Bio</label>
                <textarea wire:model.defer="bio" rows="3"
                          class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/20 rounded-xl px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all resize-none"
                          placeholder="A few words about yourself…"></textarea>
                @error('bio') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">GitHub URL</label>
                    <input wire:model.defer="githubUrl" type="url" placeholder="https://github.com/..."
                           class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 rounded-xl px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">Twitter URL</label>
                    <input wire:model.defer="twitterUrl" type="url" placeholder="https://x.com/..."
                           class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 rounded-xl px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">Website</label>
                    <input wire:model.defer="websiteUrl" type="url" placeholder="https://..."
                           class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 rounded-xl px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-all">
                </div>
            </div>

            <button wire:click="saveProfile" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-5 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-sm font-medium transition-all shadow-lg shadow-rose-500/20 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveProfile">Save Profile</span>
                <span wire:loading wire:target="saveProfile">Saving…</span>
            </button>
        </div>
    </div>

    {{-- Password section --}}
    <div class="p-6 rounded-xl bg-zinc-900/40 border border-zinc-800/60">
        <h2 class="text-sm font-semibold text-zinc-300 mb-5">Change Password</h2>

        @if(session()->has('password_success'))
        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            ✓ {{ session('password_success') }}
        </div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Current Password</label>
                <input wire:model.defer="currentPassword" type="password"
                       class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/20 rounded-xl px-3 py-2 text-sm text-zinc-100 outline-none transition-all">
                @error('currentPassword') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">New Password</label>
                    <input wire:model.defer="newPassword" type="password"
                           class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/20 rounded-xl px-3 py-2 text-sm text-zinc-100 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">Confirm Password</label>
                    <input wire:model.defer="newPasswordConfirmation" type="password"
                           class="w-full bg-zinc-800/60 border border-zinc-700 focus:border-rose-500/60 focus:ring-1 focus:ring-rose-500/20 rounded-xl px-3 py-2 text-sm text-zinc-100 outline-none transition-all">
                </div>
            </div>
            @error('newPassword') <p class="text-xs text-red-400">{{ $message }}</p> @enderror

            <button wire:click="changePassword"
                    class="px-5 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-300 text-sm font-medium transition-all">
                Update Password
            </button>
        </div>
    </div>
</div>
