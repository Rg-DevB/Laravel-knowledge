<?php

namespace App\Livewire\Voting;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VoteSystem extends Component
{
    #[Reactive]
    public Model  $model;

    public string $modelType;

    public int    $votesCount;

    public ?int   $userVote = null;

    public function mount(): void
    {
        $this->votesCount = $this->model->votes_count ?? 0;
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

            if ($this->model->user_id !== $user->id) {
                $points = $value > 0 ? 5 : -2;
                $reason = $value > 0 ? 'upvote_received' : 'downvote_received';
                $this->model->user->addReputation($points, $reason, $this->model);
            }
        }

        $this->votesCount = $this->model->fresh()->votes_count ?? 0;
    }

    public function render()
    {
        return view('livewire.voting.vote-system');
    }
}
