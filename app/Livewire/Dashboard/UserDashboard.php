<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\{Problem, Solution, Vote};

#[Layout('layouts.app')]
class UserDashboard extends Component
{
    public string $activeTab = 'overview';

    public function getStatsProperty(): array
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

    public function getMyProblemsProperty()
    {
        return auth()->user()
            ->problems()
            ->with(['category', 'tags'])
            ->withCount(['solutions', 'comments'])
            ->latest()
            ->paginate(10);
    }

    public function getMySolutionsProperty()
    {
        return auth()->user()
            ->solutions()
            ->with(['problem.category'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);
    }

    public function getRecentActivityProperty()
    {
        return auth()->user()
            ->reputationLogs()
            ->latest()
            ->limit(20)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.user-dashboard')
            ->title('Dashboard');
    }
}
