<?php

// ============================================================
// LARAVELKNOW — ALL MIGRATIONS
// Run: php artisan migrate
// ============================================================

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000001_create_users_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('username')->unique();
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->text('bio')->nullable();
    $table->string('github_url')->nullable();
    $table->string('twitter_url')->nullable();
    $table->string('website_url')->nullable();
    $table->integer('reputation')->default(0);
    $table->enum('role', ['user', 'moderator', 'admin'])->default('user');
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000002_create_categories_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('icon')->nullable();        // heroicon name
    $table->string('color')->nullable();       // tailwind color class
    $table->text('description')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// Default Laravel-specific categories:
// Eloquent, Livewire, Blade, Routing, Middleware, Queues, Jobs,
// Notifications, Broadcasting, Sanctum, Passport, API Resources,
// Policies & Gates, Caching, Redis, Horizon, Deployment, Testing

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000003_create_tags_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('color')->default('#6366f1');
    $table->integer('usage_count')->default(0);
    $table->timestamps();
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000004_create_problems_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('problems', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->constrained()->nullOnDelete();
    $table->foreignId('best_solution_id')->nullable()->constrained('solutions')->nullOnDelete();

    // Core content
    $table->string('title');
    $table->string('slug')->unique();
    $table->longText('description');          // Markdown
    $table->text('error_log')->nullable();    // Stack trace / error output
    $table->text('steps_to_reproduce')->nullable();
    $table->text('expected_behavior')->nullable();
    $table->text('actual_behavior')->nullable();

    // Laravel context
    $table->string('laravel_version')->nullable();  // e.g. "11.x", "10.x"
    $table->json('package_versions')->nullable();   // {"livewire": "3.x", "sanctum": "3.x"}
    $table->enum('project_phase', [
        'setup', 'authentication', 'database',
        'api', 'frontend', 'queues', 'deployment', 'production', 'testing'
    ])->nullable();

    // Status
    $table->enum('status', ['open', 'solved', 'closed', 'duplicate'])->default('open');

    // Engagement metrics
    $table->integer('views')->default(0);
    $table->integer('votes_count')->default(0);     // denormalized
    $table->integer('solutions_count')->default(0); // denormalized
    $table->integer('comments_count')->default(0);  // denormalized

    // Searchable
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_pinned')->default(false);

    $table->timestamps();
    $table->softDeletes();

    // Full-text index for Laravel Scout
    $table->fullText(['title', 'description', 'error_log']);
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000005_create_problem_tag_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('problem_tag', function (Blueprint $table) {
    $table->foreignId('problem_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    $table->primary(['problem_id', 'tag_id']);
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000006_create_problem_attachments_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('problem_attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('problem_id')->constrained()->cascadeOnDelete();
    $table->string('filename');
    $table->string('path');
    $table->string('mime_type');
    $table->unsignedInteger('size');
    $table->timestamps();
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000007_create_solutions_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('solutions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('problem_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->longText('content');      // Markdown
    $table->boolean('is_best')->default(false);
    $table->boolean('is_accepted')->default(false);

    // Engagement
    $table->integer('votes_count')->default(0);
    $table->integer('comments_count')->default(0);

    $table->timestamps();
    $table->softDeletes();

    $table->fullText(['content']);
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000008_create_code_snippets_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('code_snippets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solution_id')->constrained()->cascadeOnDelete();
    $table->string('language');            // php, blade, livewire, js, sql, bash
    $table->string('label')->nullable();   // "Before", "After", "Config"
    $table->longText('code');
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000009_create_comments_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->morphs('commentable');         // problems or solutions
    $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();

    $table->text('content');              // Markdown
    $table->integer('votes_count')->default(0);
    $table->boolean('is_pinned')->default(false);

    $table->timestamps();
    $table->softDeletes();
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000010_create_votes_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('votes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->morphs('votable');             // problems, solutions, comments
    $table->tinyInteger('value');          // +1 or -1
    $table->timestamps();

    $table->unique(['user_id', 'votable_id', 'votable_type']);
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000011_create_favorites_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->morphs('favoritable');         // problems, solutions
    $table->timestamps();

    $table->unique(['user_id', 'favoritable_id', 'favoritable_type']);
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000012_create_follows_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('follows', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->morphs('followable');          // problems, tags, users
    $table->timestamps();

    $table->unique(['user_id', 'followable_id', 'followable_type']);
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000013_create_reputation_logs_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('reputation_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->integer('points');
    $table->string('reason');   // 'solution_posted', 'upvote_received', 'best_solution', 'edit_accepted'
    $table->morphs('referenceable');
    $table->timestamps();
});

// Reputation points matrix:
// solution_posted      → +10
// upvote_on_solution   → +5
// downvote_on_solution → -2
// best_solution        → +25
// edit_accepted        → +5
// problem_posted       → +2
// upvote_on_problem    → +2

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000014_create_notifications_table.php
// ──────────────────────────────────────────────────────────────
// Uses Laravel's built-in notifications table
// php artisan notifications:table
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});

// ──────────────────────────────────────────────────────────────
// 2024_01_01_000015_create_edit_suggestions_table.php
// ──────────────────────────────────────────────────────────────
Schema::create('edit_suggestions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->morphs('editable');            // problems or solutions
    $table->text('original_content');
    $table->text('suggested_content');
    $table->text('reason')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('reviewed_at')->nullable();
    $table->timestamps();
});
