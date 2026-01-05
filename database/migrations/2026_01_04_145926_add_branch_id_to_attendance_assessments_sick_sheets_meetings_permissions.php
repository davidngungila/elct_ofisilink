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
        // Add branch_id to attendances table
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('user_id')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to assessments table
        if (Schema::hasTable('assessments')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('employee_id')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to sick_sheets table
        if (Schema::hasTable('sick_sheets')) {
            Schema::table('sick_sheets', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('employee_id')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to meetings table
        if (Schema::hasTable('meetings')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('category_id')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id and branch_manager_approval fields to permission_requests table
        if (Schema::hasTable('permission_requests')) {
            Schema::table('permission_requests', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('user_id')->constrained('branches')->onDelete('cascade');
                $table->boolean('branch_manager_approved')->default(false)->after('hod_reviewed');
                $table->foreignId('branch_manager_approved_by')->nullable()->after('branch_manager_approved')->constrained('users')->onDelete('set null');
                $table->timestamp('branch_manager_approved_at')->nullable()->after('branch_manager_approved_by');
                $table->text('branch_manager_comments')->nullable()->after('branch_manager_approved_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('assessments')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('sick_sheets')) {
            Schema::table('sick_sheets', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('meetings')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('permission_requests')) {
            Schema::table('permission_requests', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
                $table->dropForeign(['branch_manager_approved_by']);
                $table->dropColumn(['branch_manager_approved', 'branch_manager_approved_by', 'branch_manager_approved_at', 'branch_manager_comments']);
            });
        }
    }
};
