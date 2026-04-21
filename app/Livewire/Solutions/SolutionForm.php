<?php

namespace App\Livewire\Solutions;

use Livewire\Component;
use Livewire\Attributes\{Validate};
use App\Models\{Problem, Solution};
use Illuminate\Support\Facades\Auth;

class SolutionForm extends Component
{
    public Problem $problem;

    #[Validate('required|min:30')]
    public string $content = '';

    public array $snippets = [];

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

    public function render()
    {
        return view('livewire.solutions.solution-form');
    }
}
