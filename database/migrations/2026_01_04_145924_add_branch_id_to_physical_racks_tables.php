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
        // Add branch_id to rack_folders table
        Schema::table('rack_folders', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('department_id')->constrained('branches')->onDelete('cascade');
        });

        // Add branch_id to rack_files table
        Schema::table('rack_files', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('folder_id')->constrained('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rack_folders', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('rack_files', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
