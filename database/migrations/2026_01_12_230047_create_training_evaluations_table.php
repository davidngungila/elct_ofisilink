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
        Schema::create('training_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('overall_rating')->nullable(); // 1-5 scale
            $table->integer('content_rating')->nullable();
            $table->integer('instructor_rating')->nullable();
            $table->integer('venue_rating')->nullable();
            $table->text('what_you_liked')->nullable();
            $table->text('what_can_be_improved')->nullable();
            $table->text('additional_comments')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->timestamps();
            
            $table->unique(['training_id', 'user_id']);
            $table->index('training_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_evaluations');
    }
};
