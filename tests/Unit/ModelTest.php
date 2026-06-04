<?php

namespace Tests\Unit;

use App\Models\User;
use App\Actions\ReputationAction;
use PHPUnit\Framework\TestCase;

test('reputation action exists', function () {
    expect(class_exists(ReputationAction::class))->toBeTrue();
});

test('user model has required methods', function () {
    $user = new User();
    
    expect($user)->toHaveMethod('addReputation')
        ->and($user)->toHaveMethod('checkAndAwardBadges');
});

test('user fillable does not include sensitive fields', function () {
    $user = new User();
    $fillable = $user->getFillable();
    
    expect($fillable)->not->toContain('role')
        ->and($fillable)->not->toContain('is_admin');
});

test('models use orm correctly', function () {
    $reflection = new \ReflectionClass(\App\Models\User::class);
    $methods = $reflection->getMethods();
    
    $hasRelations = false;
    foreach ($methods as $method) {
        if (in_array($method->getName(), ['problems', 'solutions', 'comments', 'favorites'])) {
            $hasRelations = true;
            break;
        }
    }
    
    expect($hasRelations)->toBeTrue();
});
