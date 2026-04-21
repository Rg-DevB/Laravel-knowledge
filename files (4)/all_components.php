<?php

// ============================================================
// LARAVELKNOW — ALL LIVEWIRE COMPONENTS
// ============================================================

// ──────────────────────────────────────────────────────────────
// app/Livewire/Search/SearchBar.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Search;

class SearchBar extends Component
{
    public string $query = '';
    public array  $suggestions = [];
    public bool   $showSuggestions = false;

    #[On('close-suggestions')]
    public function closeSuggestions(): void
    {
        $this->showSuggestions = false;
    }

    // Debounced — fires after 300ms of typing inactivity
    #[Validate('min:2')]
    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->suggestions = [];
            $this->showSuggestions = false;
            return;
        }

        $results = Problem::search($this->query)
            ->take(6)
            ->get(['id', 'title', 'slug', 'status', 'votes_count']);

        $this->suggestions = $results->map(fn($p) => [
            'id'          => $p->id,
            'title'       => $p->title,
            'slug'        => $p->slug,
            'status'      => $p->status,
            'votes_count' => $p->votes_count,
        ])->toArray();

        $this->showSuggestions = count($this->suggestions) > 0;
    }

    public function search(): void
    {
        $this->showSuggestions = false;
        $this->redirectRoute('problems.index', ['q' => $this->query]);
    }

    public function render(): View
    {
        return view('livewire.search.search-bar');
    }
}

// ──────────────────────────────────────────────────────────────
// app/Livewire/Problems/ProblemList.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Problems;

class ProblemList extends Component
{
    public string  $search        = '';
    public string  $status        = '';
    public string  $category      = '';
    public string  $laravelVersion = '';
    public string  $sort          = 'recent'; // recent | popular | unanswered
    public array   $selectedTags  = [];
    public int     $perPage       = 15;

    protected $queryString = [
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
        'category'      => ['except' => ''],
        'laravelVersion' => ['except' => '', 'as' => 'version'],
        'sort'          => ['except' => 'recent'],
        'selectedTags'  => ['except' => [], 'as' => 'tags'],
    ];

    #[Computed]
    public function problems(): LengthAwarePaginator
    {
        $query = Problem::query()
            ->with(['user', 'category', 'tags', 'bestSolution'])
            ->withCount(['solutions', 'comments'])
            ->when($this->search,         fn($q) => $q->whereFullText(['title', 'description'], $this->search))
            ->when($this->status,         fn($q) => $q->where('status', $this->status))
            ->when($this->category,       fn($q) => $q->whereHas('category', fn($c) => $c->where('slug', $this->category)))
            ->when($this->laravelVersion, fn($q) => $q->where('laravel_version', $this->laravelVersion))
            ->when($this->selectedTags,   fn($q) => $q->whereHas('tags', fn($t) => $t->whereIn('slug', $this->selectedTags)));

        return match ($this->sort) {
            'popular'    => $query->orderByDesc('votes_count')->orderByDesc('views'),
            'unanswered' => $query->where('status', 'open')->orderByDesc('created_at'),
            default      => $query->orderByDesc('created_at'),
        };
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->get();
    }

    #[Computed]
    public function laravelVersions(): array
    {
        return ['12.x', '11.x', '10.x', '9.x', '8.x'];
    }

    public function toggleTag(string $slug): void
    {
        if (in_array($slug, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$slug]));
        } else {
            $this->selectedTags[] = $slug;
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['status', 'category', 'laravelVersion', 'selectedTags', 'search']);
    }

    public function render(): View
    {
        return view('livewire.problems.problem-list');
    }
}

// ──────────────────────────────────────────────────────────────
// app/Livewire/Problems/CreateProblem.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Problems;

class CreateProblem extends Component
{
    // Step wizard: 1 = basics, 2 = context, 3 = details
    public int $step = 1;

    // Step 1
    #[Validate('required|min:10|max:200')]
    public string $title = '';

    #[Validate('required|min:30')]
    public string $description = '';

    #[Validate('required|exists:categories,id')]
    public ?int $categoryId = null;

    #[Validate('array|max:5')]
    public array $tags = [];

    // Step 2
    public string $laravelVersion = '';
    public array  $packageVersions = [];
    public string $projectPhase = '';

    // Step 3
    public string $errorLog = '';
    public string $stepsToReproduce = '';
    public string $expectedBehavior = '';
    public string $actualBehavior = '';

    // Smart suggestions
    public array  $similarIssues = [];
    public bool   $showSimilar = false;

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->get();
    }

    #[Computed]
    public function popularTags(): Collection
    {
        return Tag::orderByDesc('usage_count')->limit(20)->get();
    }

    // Fires when title changes — AI-like duplicate detection
    public function updatedTitle(): void
    {
        if (strlen($this->title) < 15) {
            $this->showSimilar = false;
            return;
        }

        $this->similarIssues = Problem::search($this->title)
            ->take(3)
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'title'  => $p->title,
                'slug'   => $p->slug,
                'status' => $p->status,
            ])
            ->toArray();

        $this->showSimilar = count($this->similarIssues) > 0;
    }

    public function addTag(string $tagName): void
    {
        if (count($this->tags) >= 5) return;

        $tag = Tag::firstOrCreate(
            ['slug' => Str::slug($tagName)],
            ['name' => $tagName, 'color' => '#6366f1']
        );

        if (!in_array($tag->id, array_column($this->tags, 'id'))) {
            $this->tags[] = ['id' => $tag->id, 'name' => $tag->name, 'color' => $tag->color];
        }
    }

    public function removeTag(int $tagId): void
    {
        $this->tags = array_values(array_filter($this->tags, fn($t) => $t['id'] !== $tagId));
    }

    public function nextStep(): void
    {
        $this->validateOnly(match ($this->step) {
            1 => ['title', 'description', 'categoryId'],
            2 => [],
            default => [],
        });
        $this->step++;
    }

    public function save(): void
    {
        $this->validate();

        $problem = Problem::create([
            'user_id'          => auth()->id(),
            'category_id'      => $this->categoryId,
            'title'            => $this->title,
            'description'      => $this->description,
            'laravel_version'  => $this->laravelVersion,
            'package_versions' => $this->packageVersions,
            'project_phase'    => $this->projectPhase,
            'error_log'        => $this->errorLog,
            'steps_to_reproduce' => $this->stepsToReproduce,
            'expected_behavior'  => $this->expectedBehavior,
            'actual_behavior'    => $this->actualBehavior,
        ]);

        $problem->tags()->sync(array_column($this->tags, 'id'));

        auth()->user()->addReputation(2, 'problem_posted', $problem);

        Tag::whereIn('id', array_column($this->tags, 'id'))->increment('usage_count');

        $this->redirectRoute('problems.show', $problem->slug, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.problems.create-problem');
    }
}

// ──────────────────────────────────────────────────────────────
// app/Livewire/Solutions/SolutionForm.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Solutions;

class SolutionForm extends Component
{
    public Problem $problem;

    #[Validate('required|min:30')]
    public string $content = '';

    public array $snippets = []; // [{language, label, code}]

    public function addSnippet(): void
    {
        $this->snippets[] = ['language' => 'php', 'label' => '', 'code' => ''];
    }

    public function removeSnippet(int $index): void
    {
        array_splice($this->snippets, $index, 1);
    }

    public function save(): void
    {
        $this->validate();

        $solution = $this->problem->solutions()->create([
            'user_id' => auth()->id(),
            'content' => $this->content,
        ]);

        foreach ($this->snippets as $index => $snippet) {
            $solution->snippets()->create([
                'language'   => $snippet['language'],
                'label'      => $snippet['label'],
                'code'       => $snippet['code'],
                'sort_order' => $index,
            ]);
        }

        auth()->user()->addReputation(10, 'solution_posted', $solution);
        $this->problem->increment('solutions_count');

        $this->dispatch('solution-added');
        $this->reset(['content', 'snippets']);
        session()->flash('success', 'Solution posted successfully!');
    }

    public function render(): View
    {
        return view('livewire.solutions.solution-form');
    }
}

// ──────────────────────────────────────────────────────────────
// app/Livewire/Voting/VoteSystem.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Voting;

class VoteSystem extends Component
{
    public Model  $model;
    public string $modelType; // 'problem' | 'solution' | 'comment'
    public int    $votesCount;
    public ?int   $userVote = null;

    public function mount(): void
    {
        $this->votesCount = $this->model->votes_count;
        if (auth()->check()) {
            $this->userVote = auth()->user()->hasVoted($this->model);
        }
    }

    public function vote(int $value): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        $user    = auth()->user();
        $existing = $user->votes()
            ->where('votable_id', $this->model->id)
            ->where('votable_type', $this->model::class)
            ->first();

        if ($existing) {
            if ($existing->value === $value) {
                // Toggle off
                $existing->delete();
                $this->userVote = null;
            } else {
                $existing->update(['value' => $value]);
                $this->userVote = $value;
            }
        } else {
            $user->votes()->create([
                'votable_id'   => $this->model->id,
                'votable_type' => $this->model::class,
                'value'        => $value,
            ]);
            $this->userVote = $value;

            // Award reputation to model author
            if ($this->model->user_id !== $user->id) {
                $points = $value > 0 ? 5 : -2;
                $reason = $value > 0 ? 'upvote_received' : 'downvote_received';
                $this->model->user->addReputation($points, $reason, $this->model);
            }
        }

        $this->votesCount = $this->model->fresh()->votes_count;
    }

    public function render(): View
    {
        return view('livewire.voting.vote-system');
    }
}

// ──────────────────────────────────────────────────────────────
// app/Livewire/Comments/CommentThread.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Comments;

class CommentThread extends Component
{
    public Model  $commentable;
    public string $newComment = '';
    public ?int   $replyTo = null;
    public string $replyToName = '';

    #[Computed]
    public function comments(): Collection
    {
        return $this->commentable
            ->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->get();
    }

    public function replyTo(int $commentId, string $username): void
    {
        $this->replyTo = $commentId;
        $this->replyToName = $username;
    }

    public function cancelReply(): void
    {
        $this->replyTo = null;
        $this->replyToName = '';
    }

    public function postComment(): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        $this->validate(['newComment' => 'required|min:5|max:2000']);

        $this->commentable->comments()->create([
            'user_id'   => auth()->id(),
            'content'   => $this->newComment,
            'parent_id' => $this->replyTo,
        ]);

        $this->commentable->increment('comments_count');
        $this->reset(['newComment', 'replyTo', 'replyToName']);
        $this->dispatch('comment-added');
    }

    public function render(): View
    {
        return view('livewire.comments.comment-thread');
    }
}

// ──────────────────────────────────────────────────────────────
// app/Livewire/Dashboard/DashboardStats.php
// ──────────────────────────────────────────────────────────────
namespace App\Livewire\Dashboard;

class DashboardStats extends Component
{
    #[Computed]
    public function stats(): array
    {
        $user = auth()->user();
        return [
            'problems_posted'   => $user->problems()->count(),
            'solutions_posted'  => $user->solutions()->count(),
            'total_votes'       => Vote::whereHasMorph('votable', [Solution::class], fn($q) => $q->where('user_id', $user->id))->sum('value'),
            'best_solutions'    => $user->solutions()->where('is_best', true)->count(),
            'reputation'        => $user->reputation,
            'badge'             => $user->reputationBadge(),
            'problems_solved'   => $user->problems()->where('status', 'solved')->count(),
        ];
    }

    #[Computed]
    public function recentActivity(): Collection
    {
        return auth()->user()
            ->reputationLogs()
            ->latest()
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function myProblems(): Collection
    {
        return auth()->user()
            ->problems()
            ->with(['category', 'tags'])
            ->withCount('solutions')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.dashboard.dashboard-stats');
    }
}
