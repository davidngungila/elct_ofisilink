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
        Schema::create('payroll_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('formula_type')->unique(); // PAYE, NSSF, NHIF, HESLB, WCF, SDL
            $table->string('name'); // Display name
            $table->text('formula'); // The actual formula (e.g., "gross_salary * 0.10")
            $table->text('explanation'); // Detailed explanation
            $table->json('parameters')->nullable(); // Additional parameters (e.g., brackets, ceilings)
            $table->boolean('is_locked')->default(false); // Lock status
            $table->string('locked_by')->nullable(); // User who locked it
            $table->timestamp('locked_at')->nullable();
            $table->string('otp_code')->nullable(); // OTP for unlocking
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_formulas');
    }
};
