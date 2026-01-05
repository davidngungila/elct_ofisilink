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
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'gl_account_id')) {
                $table->unsignedBigInteger('gl_account_id')->nullable()->after('transaction_reference');
            }
            if (!Schema::hasColumn('payrolls', 'cash_box_id')) {
                $table->unsignedBigInteger('cash_box_id')->nullable()->after('gl_account_id');
            }
            if (!Schema::hasColumn('payrolls', 'transaction_details')) {
                $table->text('transaction_details')->nullable()->after('cash_box_id');
            }
        });
        
        // Add foreign keys only if referenced tables exist
        // Foreign keys will be added in a later migration after chart_of_accounts is created
        // See: 2025_12_04_000001_link_gl_accounts_and_cash_boxes_to_chart_of_accounts.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['gl_account_id']);
            $table->dropForeign(['cash_box_id']);
            $table->dropColumn(['gl_account_id', 'cash_box_id', 'transaction_details']);
        });
    }
};
