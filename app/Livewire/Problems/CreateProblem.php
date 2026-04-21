<?php

namespace App\Livewire\Problems;

use Livewire\Component;
use Livewire\Attributes\{Validate, Computed, Layout};
use App\Models\{Problem, Category, Tag};
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

#[Layout('layouts.app')]
class CreateProblem extends Component
{
    public int $step = 1;

    #[Validate('required|min:10|max:200')]
    public string $title = '';

    #[Validate('required|min:30')]
    public string $description = '';

    #[Validate('required|exists:categories,id')]
    public ?int $categoryId = null;

    #[Validate('array|max:5')]
    public array $tags = [];

    public string $laravelVersion = '';
    public array  $packageVersions = [];
    public string $projectPhase = '';

    public string $errorLog = '';
    public string $stepsToReproduce = '';
    public string $expectedBehavior = '';
    public string $actualBehavior = '';

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

    public function render()
    {
        return view('livewire.problems.create-problem');
    }
}
