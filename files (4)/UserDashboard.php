<?php
// ============================================================
// app/Livewire/Dashboard/UserDashboard.php
// ============================================================
namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\{Problem, Solution, Vote};

class UserDashboard extends Component
{
    public string $activeTab = 'overview'; // overview | problems | solutions | activity

    #[Computed]
    public function stats(): array
    {
        $user = auth()->user();
        return [
            'problems_posted'   => $user->problems()->count(),
            'solutions_posted'  => $user->solutions()->count(),
            'best_solutions'    => $user->solutions()->where('is_best', true)->count(),
            'problems_solved'   => $user->problems()->where('status', 'solved')->count(),
            'total_upvotes'     => Vote::whereHasMorph('votable', [Solution::class], fn($q) => $q->where('user_id', $user->id))->where('value', 1)->count(),
            'reputation'        => $user->reputation,
            'badge'             => $user->reputationBadge(),
            'next_badge_at'     => $this->nextBadgeThreshold($user->reputation),
        ];
    }

    private function nextBadgeThreshold(int $rep): array
    {
        $thresholds = [100 => 'Member', 1000 => 'Contributor', 5000 => 'Expert', 10000 => 'Legend'];
        foreach ($thresholds as $threshold => $badge) {
            if ($rep < $threshold) {
                return ['threshold' => $threshold, 'badge' => $badge, 'progress' => round(($rep / $threshold) * 100)];
            }
        }
        return ['threshold' => null, 'badge' => 'Legend', 'progress' => 100];
    }

    #[Computed]
    public function myProblems()
    {
        return auth()->user()
            ->problems()
            ->with(['category', 'tags'])
            ->withCount(['solutions', 'comments'])
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function mySolutions()
    {
        return auth()->user()
            ->solutions()
            ->with(['problem.category'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function recentActivity()
    {
        return auth()->user()
            ->reputationLogs()
            ->latest()
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function savedProblems()
    {
        return \DB::table('favorites')
            ->where('user_id', auth()->id())
            ->where('favoritable_type', Problem::class)
            ->join('problems', 'problems.id', '=', 'favorites.favoritable_id')
            ->select('problems.*', 'favorites.created_at as saved_at')
            ->latest('favorites.created_at')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.user-dashboard')
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
