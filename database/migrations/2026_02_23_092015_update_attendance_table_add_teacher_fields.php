<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate attendance for same student & date
            if (!collect(\Illuminate\Support\Facades\DB::select("SHOW INDEX FROM attendance WHERE Key_name = 'att_student_date_div_unique'"))->first()) {
                $table->unique(['student_id', 'attendance_date'], 'att_student_date_div_unique');
            }

            // Add index for faster queries
            if (!collect(\Illuminate\Support\Facades\DB::select("SHOW INDEX FROM attendance WHERE Key_name = 'att_date_idx'"))->first()) {
                $table->index(['attendance_date'], 'att_date_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('att_student_date_div_unique');

            // Drop index
            $table->dropIndex('att_date_idx');
        });
    }
};
