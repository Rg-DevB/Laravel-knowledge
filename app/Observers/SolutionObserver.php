<?php

namespace App\Observers;

use App\Models\Solution;
use App\Notifications\NewSolutionNotification;

class SolutionObserver
{
    public function created(Solution $solution): void
    {
        if ($solution->user_id !== $solution->problem->user_id) {
            $solution->problem->user->notify(new NewSolutionNotification($solution));
        }

        $solution->searchable();
    }

    public function deleted(Solution $solution): void
    {
        $solution->problem->decrement('solutions_count');
        $solution->unsearchable();
    }
}
