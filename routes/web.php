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
Route::get('/problems', ProblemList::class)->name('problems.index');
Route::get('/problems/{slug}', ProblemDetails::class)->name('problems.show');
Route::get('/u/{username}', UserProfile::class)->name('profile.show');
Route::get('/tags/{slug}', ProblemList::class)->name('tags.show');

// ── Authenticated routes ───────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/problems/create', CreateProblem::class)->name('problems.create');
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/settings', UserSettings::class)->name('settings');
});

// ── Admin routes ───────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', ModerationPanel::class)->name('dashboard');
});

// ── Auth routes (Breeze) ──────────────────────────────────────
require __DIR__.'/settings.php';
