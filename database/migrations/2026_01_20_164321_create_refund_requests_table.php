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
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->unsignedBigInteger('staff_id'); // Staff member requesting refund
            $table->string('purpose'); // Purpose/description of the expense
            $table->decimal('amount', 15, 2); // Amount to be refunded
            $table->date('expense_date'); // Date when the expense was incurred
            $table->text('description')->nullable(); // Detailed description
            $table->enum('status', [
                'pending_hod',
                'pending_accountant',
                'pending_ceo',
                'approved',
                'paid',
                'rejected'
            ])->default('pending_hod');
            
            // Approval tracking
            $table->timestamp('hod_approved_at')->nullable();
            $table->unsignedBigInteger('hod_approved_by')->nullable();
            $table->text('hod_comments')->nullable();
            
            $table->timestamp('accountant_verified_at')->nullable();
            $table->unsignedBigInteger('accountant_verified_by')->nullable();
            $table->text('accountant_comments')->nullable();
            
            $table->timestamp('ceo_approved_at')->nullable();
            $table->unsignedBigInteger('ceo_approved_by')->nullable();
            $table->text('ceo_comments')->nullable();
            
            // Payment tracking
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('payment_notes')->nullable();
            
            // Rejection tracking
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('hod_approved_by')->references('id')->on('users');
            $table->foreign('accountant_verified_by')->references('id')->on('users');
            $table->foreign('ceo_approved_by')->references('id')->on('users');
            $table->foreign('paid_by')->references('id')->on('users');
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
