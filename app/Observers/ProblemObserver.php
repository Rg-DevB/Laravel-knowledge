<?php

namespace App\Observers;

use App\Models\Problem;

class ProblemObserver
{
    public function created(Problem $problem): void
    {
        $problem->searchable();
    }

    public function updated(Problem $problem): void
    {
        $problem->searchable();
    }

    public function deleted(Problem $problem): void
    {
        $problem->unsearchable();
    }
}
