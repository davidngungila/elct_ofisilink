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
        Schema::create('meeting_resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->string('resolution_number')->nullable(); // e.g., RES-001, RES-002
            $table->string('title');
            $table->text('description');
            $table->text('resolution_text'); // The actual resolution statement
            $table->unsignedBigInteger('proposed_by')->nullable(); // Who proposed the resolution
            $table->unsignedBigInteger('seconded_by')->nullable(); // Who seconded the resolution
            $table->enum('status', ['draft', 'proposed', 'approved', 'rejected', 'deferred'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->integer('order_index')->default(0); // For ordering resolutions
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
            $table->foreign('proposed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('seconded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_resolutions');
    }
};
