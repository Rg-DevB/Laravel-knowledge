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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
