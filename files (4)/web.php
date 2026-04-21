<?php

// ============================================================
// LARAVELKNOW — routes/web.php
// ============================================================

use App\Livewire\{
    Home\LandingPage,
    Problems\ProblemList,
    Problems\ProblemDetails,
    Problems\CreateProblem,
    Solutions\SolutionPage,
    Dashboard\UserDashboard,
    Profile\UserProfile,
    Admin\ModerationPanel,
};

// ── Public routes ──────────────────────────────────────────────

Route::get('/', LandingPage::class)->name('home');

Route::get('/problems', ProblemList::class)->name('problems.index');

Route::get('/problems/{slug}', ProblemDetails::class)->name('problems.show');

Route::get('/solutions/{solution}', SolutionPage::class)->name('solutions.show');

Route::get('/u/{username}', UserProfile::class)->name('profile.show');

Route::get('/tags/{slug}', ProblemList::class)->name('tags.show');

// ── Authenticated routes ───────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/problems/create', CreateProblem::class)->name('problems.create');

    Route::get('/dashboard', UserDashboard::class)->name('dashboard');

    Route::get('/settings', \App\Livewire\Settings\UserSettings::class)->name('settings');

    // API endpoints for Livewire actions (AJAX)
    Route::post('/problems/{problem}/favorite',   [ProblemController::class, 'favorite'])->name('problems.favorite');
    Route::post('/problems/{problem}/follow',     [ProblemController::class, 'follow'])->name('problems.follow');
    Route::post('/solutions/{solution}/best',     [SolutionController::class, 'markBest'])->name('solutions.best');

});

// ── Admin routes ───────────────────────────────────────────────

Route::middleware(['auth', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', ModerationPanel::class)->name('dashboard');

    Route::get('/problems', \App\Livewire\Admin\ProblemModeration::class)->name('problems');

    Route::get('/users', \App\Livewire\Admin\UserManagement::class)->name('users');

    Route::get('/tags', \App\Livewire\Admin\TagManagement::class)->name('tags');

    Route::get('/categories', \App\Livewire\Admin\CategoryManagement::class)->name('categories');

    Route::get('/edit-suggestions', \App\Livewire\Admin\EditSuggestions::class)->name('edit-suggestions');

});

// ── Auth routes (Breeze) ──────────────────────────────────────

require __DIR__.'/auth.php';

// ============================================================
// routes/api.php  — Scout search endpoint
// ============================================================

Route::prefix('v1')->group(function () {

    // Used by SearchBar component for instant suggestions
    Route::get('/search/suggest', function (Request $request) {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Problem::search($query)->take(5)->get(['id', 'title', 'slug', 'status']);

        return response()->json($results);
    })->name('api.search.suggest');

});
