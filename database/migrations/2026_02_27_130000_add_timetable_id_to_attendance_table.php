<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add timetable_id to existing attendance table
     */
    public function up(): void
    {
        if (!Schema::hasColumn('attendance', 'timetable_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                // Add timetable_id to link with specific lecture
                $table->foreignId('timetable_id')->nullable()->after('student_id')
                      ->constrained('timetables')->onDelete('cascade');

                // Add index for better performance
                $table->index(['timetable_id', 'attendance_date']);
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attendance', 'timetable_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropForeign(['timetable_id']);
                $table->dropIndex(['timetable_id', 'attendance_date']);
                $table->dropColumn('timetable_id');
            });
        }
    }
};
