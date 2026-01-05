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
        Schema::table('meeting_minutes', function (Blueprint $table) {
            // Add approver_id if it doesn't exist (for pending approval)
            if (!Schema::hasColumn('meeting_minutes', 'approver_id')) {
                $table->unsignedBigInteger('approver_id')->nullable()->after('prepared_by');
                $table->foreign('approver_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Update status column to include 'pending_approval' if it exists, or add it
            if (Schema::hasColumn('meeting_minutes', 'status')) {
                // Status column exists, we'll update values in code, not migration
                // The status can be: 'draft', 'pending_approval', 'approved', 'rejected', 'final'
            } else {
                // Status column doesn't exist, add it
                $table->string('status')->default('draft')->after('meeting_id');
            }
            
            // Ensure approved_by exists (should already exist, but just in case)
            if (!Schema::hasColumn('meeting_minutes', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            
            // Ensure approved_at exists
            if (!Schema::hasColumn('meeting_minutes', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_minutes', 'approver_id')) {
                $table->dropForeign(['approver_id']);
                $table->dropColumn('approver_id');
            }
            // Don't drop status or approved_by as they may have been added by other migrations
        });
    }
};
