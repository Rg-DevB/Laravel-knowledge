<?php
// ============================================================
// app/Livewire/Home/LandingPage.php
// ============================================================
namespace App\Livewire\Home;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\{Problem, Solution, User, Tag, Category};

class LandingPage extends Component
{
    #[Computed]
    public function stats(): array
    {
        return [
            'problems'  => Problem::count(),
            'solutions' => Solution::count(),
            'members'   => User::count(),
            'solved'    => Problem::where('status', 'solved')->count(),
        ];
    }

    #[Computed]
    public function recentProblems()
    {
        return Problem::with(['user', 'category', 'tags'])
            ->withCount('solutions')
            ->latest()
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function popularTags()
    {
        return Tag::orderByDesc('usage_count')->limit(16)->get();
    }

    #[Computed]
    public function topContributors()
    {
        return User::orderByDesc('reputation')->limit(5)->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount('problems')->orderBy('sort_order')->get();
    }

    public function render()
    {
        return view('livewire.home.landing-page')
            ->layout('layouts.app', ['title' => 'LaravelKnow — Laravel Problem Database']);
    }
}
