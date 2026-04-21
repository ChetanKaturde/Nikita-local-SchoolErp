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
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('assignment_type');
            
            // Add index for performance
            $table->index(['is_active', 'teacher_id', 'assignment_type'], 'ta_active_teacher_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->dropIndex('ta_active_teacher_type_idx');
            $table->dropColumn('is_active');
        });
    }
};
