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
        Schema::table('standards', function (Blueprint $table) {
            // Add missing columns to support full program functionality
            // Only add columns that don't exist
            if (!Schema::hasColumn('standards', 'program_type')) {
                $table->enum('program_type', ['undergraduate', 'postgraduate', 'diploma'])
                      ->nullable()
                      ->after('code');
            }
            
            if (!Schema::hasColumn('standards', 'university_program_code')) {
                $table->string('university_program_code', 20)
                      ->nullable()
                      ->after('university_affiliation');
            }
            
            if (!Schema::hasColumn('standards', 'default_grade_scale_name')) {
                $table->string('default_grade_scale_name', 100)
                      ->nullable()
                      ->after('university_program_code');
            }
            
            if (!Schema::hasColumn('standards', 'is_active')) {
                $table->boolean('is_active')
                      ->default(true)
                      ->after('default_grade_scale_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standards', function (Blueprint $table) {
            $table->dropColumn([
                'program_type',
                'university_program_code',
                'default_grade_scale_name',
                'is_active'
            ]);
        });
    }
};