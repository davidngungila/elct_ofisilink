<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meeting_participants', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('meeting_participants', 'attendance_status')) {
                $table->string('attendance_status')->default('invited')->after('institution');
            }
            
            if (!Schema::hasColumn('meeting_participants', 'role')) {
                $table->string('role')->nullable()->after('institution');
            }
            
            if (!Schema::hasColumn('meeting_participants', 'is_required')) {
                $table->boolean('is_required')->default(true)->after('role');
            }
            
            if (!Schema::hasColumn('meeting_participants', 'invitation_sent_at')) {
                $table->timestamp('invitation_sent_at')->nullable()->after('is_required');
            }
            
            if (!Schema::hasColumn('meeting_participants', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('invitation_sent_at');
            }
        });

        // Migrate data from 'attended' boolean to 'attendance_status' string
        if (Schema::hasColumn('meeting_participants', 'attended')) {
            // Update rows where attended = true to 'attended'
            DB::table('meeting_participants')
                ->where('attended', true)
                ->update(['attendance_status' => 'attended']);
            
            // Rows where attended = false will remain as 'invited' (from default)
            // No need to update them
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_participants', function (Blueprint $table) {
            // Optionally drop the new columns if you want to rollback
            // For safety, we'll just comment this out
            // $table->dropColumn(['attendance_status', 'role', 'is_required', 'invitation_sent_at', 'checked_in_at']);
        });
    }
};
