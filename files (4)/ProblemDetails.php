<?php
// ============================================================
// app/Livewire/Problems/ProblemDetails.php
// ============================================================
namespace App\Livewire\Problems;

use Livewire\Component;
use Livewire\Attributes\{Computed, On};
use App\Models\{Problem, Solution};
use Illuminate\Support\Facades\Auth;

class ProblemDetails extends Component
{
    public Problem $problem;
    public bool $isFavorited = false;
    public bool $isFollowing = false;
    public bool $showErrorLog = false;
    public bool $showSteps    = false;

    public function mount(string $slug): void
    {
        $this->problem = Problem::where('slug', $slug)
            ->with(['user', 'category', 'tags', 'attachments', 'bestSolution.user'])
            ->withCount(['solutions', 'comments'])
            ->firstOrFail();

        // Increment view counter (once per session)
        $key = 'viewed_problem_' . $this->problem->id;
        if (!session()->has($key)) {
            $this->problem->increment('views');
            session()->put($key, true);
        }

        if (Auth::check()) {
            $this->isFavorited = Auth::user()->hasFavorited($this->problem);
            $this->isFollowing  = \DB::table('follows')
                ->where('user_id', Auth::id())
                ->where('followable_id', $this->problem->id)
                ->where('followable_type', Problem::class)
                ->exists();
        }
    }

    #[Computed]
    public function solutions()
    {
        return $this->problem->solutions()
            ->with(['user', 'snippets', 'comments.user'])
            ->withCount('comments')
            ->orderByDesc('is_best')
            ->orderByDesc('votes_count')
            ->get();
    }

    #[Computed]
    public function relatedProblems()
    {
        return Problem::search($this->problem->title)
            ->where('id', '!=', $this->problem->id)
            ->take(4)
            ->get(['id', 'title', 'slug', 'status']);
    }

    public function toggleFavorite(): void
    {
        if (!Auth::check()) { $this->redirectRoute('login'); return; }

        $params = [
            'favoritable_id'   => $this->problem->id,
            'favoritable_type' => Problem::class,
        ];

        if ($this->isFavorited) {
            Auth::user()->favorites()->where($params)->delete();
            $this->isFavorited = false;
        } else {
            Auth::user()->favorites()->create([...$params, 'user_id' => Auth::id()]);
            $this->isFavorited = true;
        }
    }

    public function toggleFollow(): void
    {
        if (!Auth::check()) { $this->redirectRoute('login'); return; }

        $params = [
            'followable_id'   => $this->problem->id,
            'followable_type' => Problem::class,
        ];

        $exists = \DB::table('follows')
            ->where('user_id', Auth::id())
            ->where($params)
            ->exists();

        if ($exists) {
            \DB::table('follows')->where('user_id', Auth::id())->where($params)->delete();
            $this->isFollowing = false;
        } else {
            \DB::table('follows')->insert([...$params, 'user_id' => Auth::id(), 'created_at' => now(), 'updated_at' => now()]);
            $this->isFollowing = true;
        }
    }

    public function markBestSolution(int $solutionId): void
    {
        $this->authorize('markBestSolution', $this->problem);
        Solution::findOrFail($solutionId)->markAsBest();
        $this->problem->refresh();
    }

    public function closeProblem(): void
    {
        $this->authorize('update', $this->problem);
        $this->problem->update(['status' => 'closed']);
    }

    public function reopenProblem(): void
    {
        $this->authorize('update', $this->problem);
        $this->problem->update(['status' => 'open']);
    }

    #[On('solution-added')]
    public function refreshSolutions(): void
    {
        $this->problem->refresh();
        unset($this->solutions);
    }

    #[On('comment-added')]
    public function refreshComments(): void
    {
        $this->problem->refresh();
    }

    public function render()
    {
        return view('livewire.problems.problem-details')
            ->layout('layouts.app', ['title' => $this->problem->title]);
    }
}
