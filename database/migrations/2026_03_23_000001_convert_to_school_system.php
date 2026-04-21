<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MIGRATION FOR INDIAN SCHOOL SYSTEM (K-10)
     * 
     * This migration adds proper support for:
     * - Standards/Classes (1-10)
     * - Divisions within each standard (A, B, C)
     * - Student capacity per division
     * 
     * USAGE FOR SCHOOLS:
     * 1. Run this migration
     * 2. Seed standards 1-10
     * 3. Create divisions A, B, C for each standard
     * 4. Assign students to divisions
     */
    public function up(): void
    {
        // Rename programs table to standards (for school context)
        if (Schema::hasTable('programs') && !Schema::hasTable('standards')) {
            Schema::rename('programs', 'standards');
        }

        // Modify standards table for school system
        Schema::table('standards', function (Blueprint $table) {
            if (!Schema::hasColumn('standards', 'standard_number')) {
                $table->tinyInteger('standard_number')->nullable()->after('short_name')
                      ->comment('1-10 for school classes');
            }

            if (Schema::hasColumn('standards', 'program_type') && !Schema::hasColumn('standards', 'education_stage')) {
                $table->renameColumn('program_type', 'education_stage');
            }

            if (!Schema::hasColumn('standards', 'board_affiliation')) {
                $table->string('board_affiliation', 50)->nullable()->after('university_affiliation')
                      ->comment('CBSE, ICSE, STATE_BOARD, etc.');
            }

            if (!Schema::hasColumn('standards', 'medium')) {
                $table->string('medium', 20)->default('English')->after('board_affiliation')
                      ->comment('English, Hindi, Marathi, etc.');
            }

            // Drop FK constraint first if it exists (may be named after old 'programs' table)
            foreach (['standards_department_id_foreign', 'programs_department_id_foreign'] as $fk) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE standards DROP FOREIGN KEY {$fk}");
                } catch (\Exception $e) {
                    // FK doesn't exist under this name, try next
                }
            }
            $table->string('department_id')->nullable()->change();
            $table->integer('duration_years')->nullable()->change();
            $table->integer('total_semesters')->nullable()->change();
        });

        // Update divisions table for school context
        Schema::table('divisions', function (Blueprint $table) {
            if (!Schema::hasColumn('divisions', 'standard_id')) {
                $table->foreignId('standard_id')->nullable()->after('academic_year_id')
                      ->constrained('standards');
            }

            if (!Schema::hasColumn('divisions', 'section')) {
                $table->string('section', 5)->nullable()->after('division_name')
                      ->comment('A, B, C, etc.');
            }

            if (!Schema::hasColumn('divisions', 'current_strength')) {
                $table->integer('current_strength')->default(0)->after('max_students')
                      ->comment('Number of students currently assigned');
            }

            if (!Schema::hasColumn('divisions', 'shift')) {
                $table->string('shift', 20)->default('morning')->after('classroom')
                      ->comment('morning, afternoon');
            }
        });

        // Update students table
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name', 100)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name', 100)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('students', 'guardian_name')) {
                $table->string('guardian_name', 100)->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('students', 'guardian_relation')) {
                $table->string('guardian_relation', 20)->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('students', 'date_of_admission')) {
                $table->date('date_of_admission')->nullable()->after('admission_date');
            }
            if (!Schema::hasColumn('students', 'previous_school')) {
                $table->string('previous_school', 200)->nullable()->after('date_of_admission');
            }
            if (!Schema::hasColumn('students', 'tc_number')) {
                $table->string('tc_number', 50)->nullable()->after('previous_school');
            }
            if (!Schema::hasColumn('students', 'health_conditions')) {
                $table->text('health_conditions')->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('students', 'requires_transport')) {
                $table->boolean('requires_transport')->default(false)->after('health_conditions');
            }
            if (!Schema::hasColumn('students', 'requires_hostel')) {
                $table->boolean('requires_hostel')->default(false)->after('requires_transport');
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Revert students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'father_name',
                'mother_name',
                'guardian_name',
                'guardian_relation',
                'date_of_admission',
                'previous_school',
                'tc_number',
                'health_conditions',
                'requires_transport',
                'requires_hostel',
            ]);
            $table->string('blood_group', 5)->change();
        });

        // Revert divisions table
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['standard_id']);
            $table->dropColumn(['standard_id', 'section', 'current_strength', 'shift']);
        });

        // Revert standards table
        Schema::table('standards', function (Blueprint $table) {
            $table->dropColumn(['standard_number', 'board_affiliation', 'medium']);
            $table->renameColumn('education_stage', 'program_type');
        });

        // Rename back to programs
        if (Schema::hasTable('standards') && !Schema::hasTable('programs')) {
            Schema::rename('standards', 'programs');
        }
    }
};
