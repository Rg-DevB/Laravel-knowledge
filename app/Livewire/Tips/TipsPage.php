<?php

namespace App\Livewire\Tips;

use Livewire\Component;
use Livewire\Attributes\{Computed, Url};

class TipsPage extends Component
{
    #[Url(as: 'cat')]
    public string $category = 'all';

    #[Url(as: 'diff')]
    public string $difficulty = 'all';

    #[Url(as: 'q')]
    public string $search = '';

    public array $readTips = [];      // IDs des tips lus (stockés en session)
    public ?int  $openTip  = null;    // ID du tip actuellement ouvert

    public function mount(): void
    {
        // Restaurer les tips lus depuis la session
        $this->readTips = session('read_tips', []);
    }

    public function toggleTip(int $id): void
    {
        $this->openTip = ($this->openTip === $id) ? null : $id;
    }

    public function markRead(int $id): void
    {
        if (!in_array($id, $this->readTips)) {
            $this->readTips[] = $id;
            session(['read_tips' => $this->readTips]);
        }
    }

    public function markUnread(int $id): void
    {
        $this->readTips = array_values(array_filter($this->readTips, fn($t) => $t !== $id));
        session(['read_tips' => $this->readTips]);
    }

    public function toggleFavorite(int $id): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $tip = \App\Models\Tip::findOrFail($id);
        if (auth()->user()->hasFavorited($tip)) {
            auth()->user()->favorites()->detach($tip->id);
        } else {
            auth()->user()->favorites()->attach($tip->id, ['favoritable_type' => \App\Models\Tip::class]);
        }
    }

    public function voteTip(int $id, int $value): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $tip = \App\Models\Tip::findOrFail($id);
        $existingVote = auth()->user()->hasVoted($tip);

        if ($existingVote === $value) {
            $tip->votes()->where('user_id', auth()->id())->delete();
        } else {
            $tip->votes()->updateOrCreate(
                ['user_id' => auth()->id()],
                ['value' => $value]
            );
        }

        $tip->updateVoteCount();
    }

    #[Computed]
    public function tips()
    {
        $userId = auth()->id();

        try {
            $query = $this->search 
                ? \App\Models\Tip::search($this->search) 
                : \App\Models\Tip::query();

            // Note: Scout search results don't support traditional eloquent builder methods 
            // directly in the same way without querying the database for the search results first,
            // or using Scout's query builder.
            if ($this->search) {
                $tips = $query->query(function ($builder) {
                    $builder->where('is_approved', true)
                            ->when($this->category !== 'all', fn($q) => $q->where('category', $this->category))
                            ->when($this->difficulty !== 'all', fn($q) => $q->where('difficulty', $this->difficulty))
                            ->withCount('comments');
                })->get();
            } else {
                $tips = clone $query;
                $tips = $tips->where('is_approved', true)
                    ->when($this->category !== 'all', fn($q) => $q->where('category', $this->category))
                    ->when($this->difficulty !== 'all', fn($q) => $q->where('difficulty', $this->difficulty))
                    ->withCount('comments')
                    ->latest()
                    ->get();
            }
        } catch (\Exception $e) {
            $tips = \App\Models\Tip::where('is_approved', true)
                ->when($this->search, fn($q) => $q->where(fn($sub) => $sub->where('title', 'like', "%{$this->search}%")->orWhere('why', 'like', "%{$this->search}%")))
                ->when($this->category !== 'all', fn($q) => $q->where('category', $this->category))
                ->when($this->difficulty !== 'all', fn($q) => $q->where('difficulty', $this->difficulty))
                ->withCount('comments')
                ->latest()
                ->get();
        }

        return $tips->map(fn($t) => [
                'id'         => $t->id,
                'category'   => $t->category,
                'title'      => $t->title,
                'difficulty' => $t->difficulty,
                'why'        => $t->why,
                'votes_count'=> $t->votes_count,
                'comments_count' => $t->comments_count,
                'is_favorited' => $userId ? \DB::table('favorites')
                    ->where('user_id', $userId)
                    ->where('favoritable_id', $t->id)
                    ->where('favoritable_type', \App\Models\Tip::class)
                    ->exists() : false,
                'user_vote'  => $userId ? \DB::table('votes')
                    ->where('user_id', $userId)
                    ->where('votable_id', $t->id)
                    ->where('votable_type', \App\Models\Tip::class)
                    ->value('value') : null,
                'junior'     => [
                    'label' => $t->junior_label,
                    'lang'  => 'php',
                    'code'  => $t->junior_code,
                ],
                'senior'     => [
                    'label' => $t->senior_label,
                    'lang'  => 'php',
                    'code'  => $t->senior_code,
                ],
                'pros'       => $t->pros ?? [],
            ]);
    }

    #[Computed]
    public function readCount(): int
    {
        return count($this->readTips);
    }

    #[Computed]
    public function totalCount(): int
    {
        return \App\Models\Tip::where('is_approved', true)->count();
    }

    #[Computed]
    public function progressPercent(): int
    {
        if ($this->totalCount === 0) return 0;
        return (int) round(($this->readCount / $this->totalCount) * 100);
    }

    #[Computed]
    public function progressBadge(): string
    {
        return match (true) {
            $this->readCount >= 20 => '🏆 Tip Master',
            $this->readCount >= 10 => '🧠 Senior Mindset',
            $this->readCount >= 5  => '📈 En progression',
            default                => '🆕 Newcomer',
        };
    }

    #[Computed]
    public function categories(): array
    {
        return [
            'all'          => ['label' => 'Tous',         'color' => 'zinc'],
            'eloquent'     => ['label' => 'Eloquent',      'color' => 'orange'],
            'performance'  => ['label' => 'Performance',   'color' => 'amber'],
            'security'     => ['label' => 'Security',      'color' => 'blue'],
            'livewire'     => ['label' => 'Livewire',      'color' => 'violet'],
            'architecture' => ['label' => 'Architecture',  'color' => 'emerald'],
            'blade'        => ['label' => 'Blade',         'color' => 'red'],
            'testing'      => ['label' => 'Testing',       'color' => 'green'],
        ];
    }

    // ── Database driven ─────────────────────────────────────────

    public function render()
    {
        return view('livewire.tips.tips-page')
            ->layout('layouts.app', ['title' => 'Tips Junior → Senior — Knowravel']);
    }
}
