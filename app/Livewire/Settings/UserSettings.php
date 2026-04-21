<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class UserSettings extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public ?string $bio = '';
    public ?string $github_url = '';
    public ?string $twitter_url = '';
    public ?string $website_url = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->bio = $user->bio;
        $this->github_url = $user->github_url;
        $this->twitter_url = $user->twitter_url;
        $this->website_url = $user->website_url;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . auth()->id(),
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'bio' => 'nullable|string|max:500',
            'github_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'bio' => $this->bio,
            'github_url' => $this->github_url,
            'twitter_url' => $this->twitter_url,
            'website_url' => $this->website_url,
        ]);

        session()->flash('success', 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.settings.user-settings')
            ->title('Settings');
    }
}
