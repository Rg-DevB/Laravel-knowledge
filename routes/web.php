<?php

use App\Http\Middleware\RoleMiddleware;
use App\Livewire\Admin\ModerationPanel;
use App\Livewire\Dashboard\UserDashboard;
use App\Livewire\Home\LandingPage;
use App\Livewire\Problems\CreateProblem;
use App\Livewire\Problems\ProblemDetails;
use App\Livewire\Problems\ProblemList;
use App\Livewire\Profile\UserProfile;
use App\Livewire\Settings\UserSettings;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────────
Route::get('/', LandingPage::class)->name('home');
Route::get('/problems', ProblemList::class)->name('problems.index')->middleware('throttle:30,1');
Route::get('/problems/{slug}', ProblemDetails::class)->name('problems.show')->middleware('throttle:60,1');
Route::get('/u/{username}', UserProfile::class)->name('profile.show')->middleware('throttle:30,1');
Route::get('/tags/{slug}', ProblemList::class)->name('tags.show')->middleware('throttle:30,1');

// ── Authenticated routes ───────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/problems/create', CreateProblem::class)->name('problems.create')->middleware('throttle:20,1');
    Route::get('/dashboard', UserDashboard::class)->name('dashboard')->middleware('throttle:30,1');
    Route::get('/settings', UserSettings::class)->name('settings')->middleware('throttle:20,1');
});

// ── Admin routes ───────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', ModerationPanel::class)->name('dashboard')->middleware('throttle:30,1');
});

// ── Auth routes (Breeze) ──────────────────────────────────────
require __DIR__.'/settings.php';
