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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solutions');
    }
};
