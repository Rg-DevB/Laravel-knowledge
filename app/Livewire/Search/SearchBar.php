<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\Attributes\{Computed, On, Validate};
use App\Models\Problem;

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

    public function render()
    {
        return view('livewire.search.search-bar');
    }
}
