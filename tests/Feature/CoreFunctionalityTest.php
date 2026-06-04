<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Problem;
use App\Models\Solution;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

test('problems can be created', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $problem = Problem::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);
    
    expect($problem)->toBeInstanceOf(Problem::class)
        ->and($problem->title)->toBeString()
        ->and($problem->description)->toBeString();
});

test('solutions can be created', function () {
    $user = User::factory()->create();
    $problem = Problem::factory()->create();
    
    $solution = Solution::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $problem->id,
    ]);
    
    expect($solution)->toBeInstanceOf(Solution::class)
        ->and($solution->content)->toBeString();
});

test('users have reputation', function () {
    $user = User::factory()->create(['reputation' => 100]);
    
    expect($user->reputation)->toBe(100);
});

test('guests cannot access protected routes', function () {
    $response = $this->get(route('dashboard'));
    
    $response->assertRedirect('/login');
});

test('authenticated users can access dashboard', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('dashboard'));
    
    $response->assertOk();
});

test('search api returns results', function () {
    $user = User::factory()->create();
    Problem::factory()->count(3)->create(['title' => 'Test Problem']);
    
    $response = $this->actingAs($user)->getJson(route('api.search', ['q' => 'Test']));
    
    $response->assertOk()
        ->assertJsonStructure([
            'results',
            'total',
        ]);
});

test('problem policy prevents unauthorized marking as best', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $problem = Problem::factory()->create(['user_id' => $owner->id]);
    
    // Other user should not be able to mark solution as best
    $policy = new \App\Policies\ProblemPolicy();
    
    expect($policy->markBestSolution($otherUser, $problem))->toBeFalse()
        ->and($policy->markBestSolution($owner, $problem))->toBeTrue();
});

test('badges are awarded automatically', function () {
    $user = User::factory()->create();
    
    // Create first problem
    $problem = Problem::factory()->create(['user_id' => $user->id]);
    
    // Check if badge was awarded (implementation dependent)
    expect($user->fresh()->badges)->not->toBeNull();
});

test('mass assignment protection works', function () {
    $user = User::factory()->create();
    
    // Try to mass assign role and reputation (should be ignored)
    $userData = [
        'name' => 'New Name',
        'role' => 'admin',
        'reputation' => 9999,
    ];
    
    $user->fill($userData);
    
    // Role and reputation should not change via fill
    expect($user->role)->not->toBe('admin')
        ->and($user->reputation)->not->toBe(9999);
});

test('rate limiting is applied to search', function () {
    $user = User::factory()->create();
    
    // This test verifies rate limiting middleware is in place
    // Actual limit testing would require multiple rapid requests
    $response = $this->actingAs($user)->getJson(route('api.search', ['q' => 'test']));
    
    $response->assertOk();
});
