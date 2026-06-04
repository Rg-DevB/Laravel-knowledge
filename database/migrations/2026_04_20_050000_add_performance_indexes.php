<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add performance indexes to problems table
        Schema::table('problems', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'problems_status_created_at');
            $table->index(['category_id', 'status'], 'problems_category_status');
            $table->index(['laravel_version', 'status'], 'problems_version_status');
            $table->index(['votes_count', 'views'], 'problems_popular');
            $table->index('user_id', 'problems_user_id');
        });

        // Add performance indexes to solutions table
        Schema::table('solutions', function (Blueprint $table) {
            $table->index(['problem_id', 'is_best'], 'solutions_problem_best');
            $table->index(['user_id', 'created_at'], 'solutions_user_created');
            $table->index('votes_count', 'solutions_votes');
        });

        // Add performance indexes to votes table
        Schema::table('votes', function (Blueprint $table) {
            $table->index(['votable_type', 'votable_id'], 'votes_votable');
            $table->index(['user_id', 'value'], 'votes_user_value');
        });

        // Add performance indexes to comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['commentable_type', 'commentable_id'], 'comments_commentable');
            $table->index('parent_id', 'comments_parent');
        });

        // Add performance indexes to favorites table
        Schema::table('favorites', function (Blueprint $table) {
            $table->index(['favoritable_type', 'favoritable_id'], 'favorites_favoritable');
        });

        // Add performance indexes to follows table
        Schema::table('follows', function (Blueprint $table) {
            $table->index(['followable_type', 'followable_id'], 'follows_followable');
        });

        // Add performance indexes to reputation_logs table
        Schema::table('reputation_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'reputation_logs_user_created');
            $table->index('reason', 'reputation_logs_reason');
        });

        // Add performance indexes to tags table
        Schema::table('tags', function (Blueprint $table) {
            $table->index('usage_count', 'tags_usage_count');
        });

        // Add index to users table for reputation lookups
        Schema::table('users', function (Blueprint $table) {
            $table->index('reputation', 'users_reputation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropIndex('problems_status_created_at');
            $table->dropIndex('problems_category_status');
            $table->dropIndex('problems_version_status');
            $table->dropIndex('problems_popular');
            $table->dropIndex('problems_user_id');
        });

        Schema::table('solutions', function (Blueprint $table) {
            $table->dropIndex('solutions_problem_best');
            $table->dropIndex('solutions_user_created');
            $table->dropIndex('solutions_votes');
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex('votes_votable');
            $table->dropIndex('votes_user_value');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_commentable');
            $table->dropIndex('comments_parent');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex('favorites_favoritable');
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->dropIndex('follows_followable');
        });

        Schema::table('reputation_logs', function (Blueprint $table) {
            $table->dropIndex('reputation_logs_user_created');
            $table->dropIndex('reputation_logs_reason');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex('tags_usage_count');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_reputation');
        });
    }
};
