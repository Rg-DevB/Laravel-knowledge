<?php
// ============================================================
// app/Livewire/Admin/ModerationPanel.php
// ============================================================
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\{Problem, Solution, User, EditSuggestion};

class ModerationPanel extends Component
{
    public string $activeTab = 'overview';

    #[Computed]
    public function stats(): array
    {
        return [
            'total_problems'      => Problem::count(),
            'open_problems'       => Problem::where('status', 'open')->count(),
            'solved_problems'     => Problem::where('status', 'solved')->count(),
            'total_users'         => User::count(),
            'total_solutions'     => Solution::count(),
            'pending_suggestions' => EditSuggestion::where('status', 'pending')->count(),
            'reported_content'    => 0, // Extend with reports table
        ];
    }

    #[Computed]
    public function recentProblems()
    {
        return Problem::with('user', 'category')
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function pendingSuggestions()
    {
        return EditSuggestion::with(['user', 'editable'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function recentUsers()
    {
        return User::latest()->paginate(15);
    }

    public function approveSuggestion(int $id): void
    {
        $suggestion = EditSuggestion::findOrFail($id);
        $suggestion->editable->update(['content' => $suggestion->suggested_content]);
        $suggestion->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        $suggestion->user->addReputation(5, 'edit_accepted', $suggestion);
    }

    public function rejectSuggestion(int $id): void
    {
        EditSuggestion::findOrFail($id)->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function deleteProblem(int $id): void
    {
        Problem::findOrFail($id)->delete();
    }

    public function banUser(int $id): void
    {
        User::findOrFail($id)->update(['role' => 'banned']);
    }

    public function render()
    {
        return view('livewire.admin.moderation-panel')
            ->layout('layouts.app', ['title' => 'Admin — Moderation']);
    }
}

// ============================================================
// app/Livewire/Settings/UserSettings.php
// ============================================================
namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class UserSettings extends Component
{
    use WithFileUploads;

    #[Validate('required|min:2|max:100')]
    public string $name = '';

    #[Validate('required|min:3|max:30|alpha_dash')]
    public string $username = '';

    #[Validate('nullable|max:300')]
    public string $bio = '';

    #[Validate('nullable|url')]
    public string $githubUrl = '';

    #[Validate('nullable|url')]
    public string $twitterUrl = '';

    #[Validate('nullable|url')]
    public string $websiteUrl = '';

    #[Validate('nullable|image|max:2048')]
    public $avatar = null;

    // Password
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public bool $saved = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name       = $user->name;
        $this->username   = $user->username;
        $this->bio        = $user->bio ?? '';
        $this->githubUrl  = $user->github_url ?? '';
        $this->twitterUrl = $user->twitter_url ?? '';
        $this->websiteUrl = $user->website_url ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name'     => 'required|min:2|max:100',
            'username' => 'required|min:3|max:30|alpha_dash|unique:users,username,' . auth()->id(),
            'bio'      => 'nullable|max:300',
            'githubUrl'  => 'nullable|url',
            'twitterUrl' => 'nullable|url',
            'websiteUrl' => 'nullable|url',
        ]);

        $data = [
            'name'        => $this->name,
            'username'    => $this->username,
            'bio'         => $this->bio,
            'github_url'  => $this->githubUrl,
            'twitter_url' => $this->twitterUrl,
            'website_url' => $this->websiteUrl,
        ];

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $data['avatar'] = \Storage::url($path);
        }

        auth()->user()->update($data);
        $this->saved = true;
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword'         => 'required',
            'newPassword'             => 'required|min:8|confirmed',
            'newPasswordConfirmation' => 'required',
        ]);

        if (!\Hash::check($this->currentPassword, auth()->user()->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update(['password' => \Hash::make($this->newPassword)]);
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        session()->flash('password_success', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.settings.user-settings')
            ->layout('layouts.app', ['title' => 'Settings']);
    }
}
