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
        Schema::table('permission_requests', function (Blueprint $table) {
            $table->foreignId('training_id')->nullable()->after('reason_description')
                  ->constrained('trainings')->nullOnDelete();
            $table->boolean('is_for_training')->default(false)->after('training_id');
            $table->index('training_id');
            $table->index('is_for_training');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permission_requests', function (Blueprint $table) {
            $table->dropForeign(['training_id']);
            $table->dropColumn(['training_id', 'is_for_training']);
        });
    }
};
