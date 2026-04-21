<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create basic roles
        $roles = [
            'principal' => 'Principal',
            'admin' => 'Administrator',
            'hod_commerce' => 'HOD Commerce',
            'hod_science' => 'HOD Science',
            'hod_management' => 'HOD Management',
            'hod_arts' => 'HOD Arts',
            'class_teacher' => 'Class Teacher',
            'subject_teacher' => 'Subject Teacher',
            'teacher' => 'Teacher',
            'staff' => 'Staff',
            'lab_instructor' => 'Lab Instructor',
            'accounts_staff' => 'Accounts Staff',
            'accountant' => 'Accountant',
            'admission_officer' => 'Admission Officer',
            'student' => 'Student',
            'parent' => 'Parent',
            'office' => 'Office Staff',
            'librarian' => 'Librarian',
        ];

        foreach ($roles as $name => $displayName) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ]);
        }

        // Create basic permissions
        $permissions = [
            // Student Management
            'view_students',
            'create_students', 
            'edit_students',
            'delete_students',
            
            // Fee Management
            'view_fees',
            'collect_fees',
            'manage_fee_structures',
            
            // Academic Management
            'manage_divisions',
            'manage_subjects',
            'assign_teachers',
            
            // Results & Marks
            'enter_marks',
            'view_results',
            'generate_marksheets',
            
            // Reports
            'view_reports',
            'generate_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign permissions to roles
        $principal = Role::findByName('principal');
        $principal->givePermissionTo(Permission::all());
    }
}