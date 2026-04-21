<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;

#[Layout('layouts.app')]
class UserProfile extends Component
{
    public User $user;
    public string $activeTab = 'problems';

    public function mount(string $username): void
    {
        $this->user = User::where('username', $username)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.profile.user-profile')
            ->title($this->user->name . ' — Profile');
    }
}
