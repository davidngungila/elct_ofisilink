<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_categories')) {
            Schema::create('asset_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('code')->unique();
                $table->integer('depreciation_years')->default(5);
                $table->decimal('depreciation_rate', 5, 2)->default(20.00); // Annual depreciation percentage
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            // Table exists, add missing columns if they don't exist
            Schema::table('asset_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('asset_categories', 'code')) {
                    $table->string('code')->unique()->after('description');
                }
                if (!Schema::hasColumn('asset_categories', 'depreciation_years')) {
                    $table->integer('depreciation_years')->default(5)->after('code');
                }
                if (!Schema::hasColumn('asset_categories', 'depreciation_rate')) {
                    $table->decimal('depreciation_rate', 5, 2)->default(20.00)->after('depreciation_years');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};

