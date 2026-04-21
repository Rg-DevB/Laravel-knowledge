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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
