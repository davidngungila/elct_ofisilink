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
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('category')->nullable()->after('topic');
            $table->text('objectives')->nullable()->after('content');
            $table->integer('max_participants')->nullable()->after('end_date');
            $table->decimal('cost', 10, 2)->nullable()->after('max_participants');
            $table->boolean('requires_certificate')->default(false)->after('cost');
            $table->boolean('send_notifications')->default(true)->after('requires_certificate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['category', 'objectives', 'max_participants', 'cost', 'requires_certificate', 'send_notifications']);
        });
    }
};
