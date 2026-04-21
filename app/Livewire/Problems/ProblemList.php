<?php

namespace App\Livewire\Problems;

use Livewire\Component;
use Livewire\Attributes\{Computed, QueryString, Layout};
use App\Models\Problem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ProblemList extends Component
{
    #[QueryString]
    public string  $search        = '';

    #[QueryString]
    public string  $status        = '';

    #[QueryString]
    public string  $category      = '';

    #[QueryString]
    public string  $laravelVersion = '';

    #[QueryString]
    public string  $sort          = 'recent';

    #[QueryString]
    public array   $selectedTags  = [];

    public int     $perPage       = 15;

    #[Layout('layouts.app')]
    #[Computed]
    public function problems(): LengthAwarePaginator
    {
        $query = Problem::query()
            ->with(['user', 'category', 'tags', 'bestSolution'])
            ->withCount(['solutions', 'comments'])
            ->when($this->search, fn($q) => $q->whereFullText(['title', 'description'], $this->search))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->category, fn($q) => $q->whereHas('category', fn($c) => $c->where('slug', $this->category)))
            ->when($this->laravelVersion, fn($q) => $q->where('laravel_version', $this->laravelVersion))
            ->when($this->selectedTags, fn($q) => $q->whereHas('tags', fn($t) => $t->whereIn('slug', $this->selectedTags)));

        $query = match ($this->sort) {
            'popular'    => $query->orderByDesc('votes_count')->orderByDesc('views'),
            'unanswered' => $query->where('status', 'open')->orderByDesc('created_at'),
            default      => $query->orderByDesc('created_at'),
        };

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function categories(): Collection
    {
        return \App\Models\Category::orderBy('sort_order')->get();
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

    public function render()
    {
        return view('livewire.problems.problem-list');
    }
}
