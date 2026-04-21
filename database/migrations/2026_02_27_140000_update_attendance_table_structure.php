<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Update existing attendance table to use proper structure:
     * - Rename attendance_date to date (if not already renamed)
     * - Add ip_address field if not exists
     * - Ensure proper indexes exist
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // DB column is attendance_date — do NOT rename it

            // Add ip_address if not exists
            if (!Schema::hasColumn('attendance', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('remarks');
            }

            // Add indexes if they don't exist
            $indexes = DB::select("SHOW INDEX FROM attendance");
            $indexNames = array_column($indexes, 'Key_name');

            if (!in_array('attendance_date_student_id_index', $indexNames)) {
                $table->index(['attendance_date', 'student_id'], 'attendance_date_student_id_index');
            }

            if (Schema::hasColumn('attendance', 'timetable_id')) {
                if (!in_array('attendance_timetable_id_date_index', $indexNames)) {
                    $table->index(['timetable_id', 'attendance_date'], 'attendance_timetable_id_date_index');
                }
            }

            if (!in_array('attendance_marked_by_date_index', $indexNames)) {
                $table->index(['marked_by', 'attendance_date'], 'attendance_marked_by_date_index');
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'ip_address')) {
                $table->dropColumn('ip_address');
            }

            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM attendance");
            $indexNames = array_column($indexes, 'Key_name');

            if (in_array('attendance_date_student_id_index', $indexNames)) {
                $table->dropIndex('attendance_date_student_id_index');
            }
            if (in_array('attendance_timetable_id_date_index', $indexNames)) {
                $table->dropIndex('attendance_timetable_id_date_index');
            }
            if (in_array('attendance_marked_by_date_index', $indexNames)) {
                $table->dropIndex('attendance_marked_by_date_index');
            }
        });
    }
};
