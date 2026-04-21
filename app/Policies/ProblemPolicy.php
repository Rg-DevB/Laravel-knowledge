<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Problem;

class ProblemPolicy
{
    public function update(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id || in_array($user->role, ['admin', 'moderator']);
    }

    public function delete(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id || in_array($user->role, ['admin', 'moderator']);
    }

    public function markBestSolution(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id;
    }
}
