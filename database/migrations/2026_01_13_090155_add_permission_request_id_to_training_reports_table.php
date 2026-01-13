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
        Schema::table('training_reports', function (Blueprint $table) {
            $table->foreignId('permission_request_id')->nullable()->after('training_id')
                  ->constrained('permission_requests')->nullOnDelete();
            $table->index('permission_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_reports', function (Blueprint $table) {
            $table->dropForeign(['permission_request_id']);
            $table->dropColumn('permission_request_id');
        });
    }
};
