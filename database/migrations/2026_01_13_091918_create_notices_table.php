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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('priority', ['normal', 'important', 'urgent'])->default('normal');
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('show_to_all')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('require_acknowledgment')->default(false);
            $table->boolean('allow_redisplay')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('notice_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('notice_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('acknowledged_at');
            $table->timestamps();
            
            $table->unique(['notice_id', 'user_id']);
        });

        Schema::create('notice_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('file_path');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_attachments');
        Schema::dropIfExists('notice_acknowledgments');
        Schema::dropIfExists('notice_role');
        Schema::dropIfExists('notices');
    }
};
