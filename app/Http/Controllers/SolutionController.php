<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolutionController extends Controller
{
    public function markBest(Solution $solution): \Illuminate\Http\RedirectResponse
    {
        // Verify the solution belongs to a problem the user owns
        $problem = $solution->problem;
        
        if (!Auth::check() || Auth::id() !== $problem->user_id) {
            abort(403, 'Unauthorized action');
        }
        
        $solution->markAsBest();
        
        // Award badge to the solution author if this is their first best solution
        $author = $solution->author;
        $bestSolutionBadge = \App\Models\Badge::where('slug', 'first-best-solution')->first();
        if ($bestSolutionBadge && $author) {
            $author->awardBadge($bestSolutionBadge, "Best solution for problem #{$problem->id}");
        }
        
        // Check for other badges
        if ($author) {
            $author->checkAndAwardBadges();
        }
        
        return redirect()->back();
    }
}
