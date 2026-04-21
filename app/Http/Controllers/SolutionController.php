<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function markBest(Solution $solution): \Illuminate\Http\RedirectResponse
    {
        $solution->markAsBest();
        return redirect()->back();
    }
}
