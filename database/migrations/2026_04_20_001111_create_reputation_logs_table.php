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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reputation_logs');
    }
};
