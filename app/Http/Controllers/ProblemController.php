<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function favorite(Problem $problem): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        $exists = \DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('favoritable_id', $problem->id)
            ->where('favoritable_type', Problem::class)
            ->exists();

        if ($exists) {
            \DB::table('favorites')
                ->where('user_id', $user->id)
                ->where('favoritable_id', $problem->id)
                ->where('favoritable_type', Problem::class)
                ->delete();
        } else {
            \DB::table('favorites')->insert([
                'user_id' => $user->id,
                'favoritable_id' => $problem->id,
                'favoritable_type' => Problem::class,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back();
    }

    public function follow(Problem $problem): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        $exists = \DB::table('follows')
            ->where('user_id', $user->id)
            ->where('followable_id', $problem->id)
            ->where('followable_type', Problem::class)
            ->exists();

        if ($exists) {
            \DB::table('follows')
                ->where('user_id', $user->id)
                ->where('followable_id', $problem->id)
                ->where('followable_type', Problem::class)
                ->delete();
        } else {
            \DB::table('follows')->insert([
                'user_id' => $user->id,
                'followable_id' => $problem->id,
                'followable_type' => Problem::class,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back();
    }
}
