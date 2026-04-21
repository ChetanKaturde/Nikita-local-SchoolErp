<?php

/**
 * =============================================================================
 * RULE CONFIGURATIONS TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'rule_configurations' table for storing
 * institution-specific rule configuration values. This allows different
 * academic sessions or programs to have different rule values.
 *
 * PURPOSE:
 * - Store rule values per academic session/program
 * - Enable rule overrides for specific contexts
 * - Track rule configuration history
 * - Support rule value inheritance
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Rule configuration layer (knowledge_base.md §2.2)
 * - Institution-level flexibility (knowledge_base.md §12)
 * - Configurable academic settings (knowledge_base.md §5)
 * =============================================================================
 */

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
        Schema::create('rule_configurations', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // RULE REFERENCE
            // ========================================
            // Link to academic rule
            $table->foreignId('academic_rule_id')->constrained('academic_rules')->onDelete('cascade');

            // ========================================
            // CONTEXT SCOPING
            // ========================================
            // Academic session this configuration applies to (null = all sessions)
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions');

            // Program this configuration applies to (null = all programs)
            $table->foreignId('program_id')->nullable()->constrained('programs');

            // Department this configuration applies to (null = all departments)
            $table->foreignId('department_id')->nullable()->constrained('departments');

            // ========================================
            // CONFIGURATION VALUE
            // ========================================
            // The configured value (overrides the default rule value)
            $table->text('value');

            // ========================================
            // INHERITANCE & OVERRIDES
            // ========================================
            // Is this an override of the default value?
            $table->boolean('is_override')->default(false);

            // Override reason (if this is an override)
            $table->text('override_reason')->nullable();

            // User who approved the override
            $table->foreignId('override_approved_by')->nullable()->constrained('users');

            // When the override was approved
            $table->timestamp('override_approved_at')->nullable();

            // ========================================
            // EFFECTIVE DATES
            // ========================================
            // When this configuration becomes effective
            $table->date('effective_from')->nullable();

            // When this configuration expires
            $table->date('effective_to')->nullable();

            // ========================================
            // STATUS
            // ========================================
            // Is this configuration active?
            $table->boolean('is_active')->default(true);

            // ========================================
            // AUDIT FIELDS
            // ========================================
            // User who created this configuration
            $table->foreignId('created_by')->nullable()->constrained('users');

            // User who last updated this configuration
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // ========================================
            // TIMESTAMPS & SOFT DELETE
            // ========================================
            $table->timestamps();
            $table->softDeletes();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Unique constraint: One configuration per rule per context
            $table->unique([
                'academic_rule_id',
                'academic_session_id',
                'program_id',
                'department_id'
            ], 'unique_rule_context');

            // Index for rule lookup
            $table->index('academic_rule_id', 'rc_rule_idx');

            // Index for session lookup
            $table->index('academic_session_id', 'rc_session_idx');

            // Index for program lookup
            $table->index('program_id', 'rc_program_idx');

            // Index for active configurations
            $table->index('is_active', 'rc_active_idx');

            // Index for override tracking
            $table->index(['is_override', 'override_approved_by'], 'rc_override_idx');

            // Composite index for rule resolution
            $table->index([
                'academic_rule_id',
                'academic_session_id',
                'program_id',
                'is_active'
            ], 'rule_resolution_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_configurations');
    }
};
