<?php

namespace App\Http\Controllers;

use App\Models\Solution;
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
        return redirect()->back();
    }
}
