# Teacher & Student Password Generation Logic

## Overview

This document explains how passwords are automatically generated for teachers and students in the School ERP system.

---

## Password Generation Flow

### 1. **Password Helper Class**

**File:** `app/Helpers/PasswordHelper.php`

```php
<?php

namespace App\Helpers;

class PasswordHelper
{
    /**
     * Generate a random password
     *
     * @param int $length Password length
     * @return string
     */
    public static function generate($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Generate password in format: First3LettersOfName + 4RandomDigits
     * Example: RAH1234
     *
     * @param string $firstName
     * @return string
     */
    public static function generateFormatted($firstName)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $firstName), 0, 3));
        $suffix = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $suffix;
    }
}
```

**Features:**
- Generates alphanumeric passwords (a-z, A-Z, 0-9)
- Default length: 8 characters
- Uses cryptographically secure `random_int()`
- Optional formatted generation (e.g., `RAH1234`)

---

### 2. **Teacher Password Generation**

**File:** `app/Http/Controllers/Web/TeacherController.php`

```php
public function store(Request $request)
{
    // If password is not provided, generate one
    if ($request->filled('password')) {
        $password = $request->input('password');
        $generatedPassword = $password;
    } else {
        $generatedPassword = PasswordHelper::generate(10);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'nullable|string|min:8|confirmed',
        // ... other validations
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($generatedPassword),
        'temp_password' => $generatedPassword,           // Plain text (for admin viewing)
        'password_generated_at' => now(),                // Timestamp
    ]);

    $user->assignRole('teacher');

    return redirect()->route('dashboard.teachers.index')
        ->with('success', 'Teacher created successfully! Password: ' . $generatedPassword);
}
```

**Process:**
1. Check if admin provided a custom password
2. If not, generate random 10-character password using `PasswordHelper::generate(10)`
3. Store **hashed** version in `password` field (for login authentication)
4. Store **plain text** version in `temp_password` field (for admin viewing)
5. Record timestamp in `password_generated_at`
6. Assign 'teacher' role

---

### 3. **Student Password Generation**

**File:** `app/Http/Controllers/Web/StudentController.php`

```php
public function store(Request $request)
{
    // ... validation code ...

    // Generate password for student
    $generatedPassword = PasswordHelper::generate(10);
    $hashedPassword = Hash::make($generatedPassword);

    // ... file uploads ...

    $student = Student::create($validated);

    // Create user account for student with generated password
    $studentEmail = $request->input('email') ?? 
        strtolower($validated['first_name'] . '.' . $validated['last_name'] . $student->id . '@student.schoolerp.com');

    User::create([
        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
        'email' => $studentEmail,
        'password' => $hashedPassword,           // Hashed for login
        'temp_password' => $generatedPassword,    // Plain text for admin
        'password_generated_at' => now(),         // Timestamp
        'email_verified_at' => now(),
    ])->assignRole('student');

    return redirect()
        ->route('dashboard.students.show', $student)
        ->with('success', 'Student created successfully...');
}
```

**Process:**
1. Always auto-generate 10-character random password
2. Create student record first (to get ID)
3. Generate email if not provided: `firstname.lastname{ID}@student.schoolerp.com`
4. Store **hashed** password in `password` field
5. Store **plain text** in `temp_password` field
6. Auto-verify email (`email_verified_at = now()`)
7. Assign 'student' role

---

## Database Schema

**Table:** `users`

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),              -- Bcrypt hashed
    temp_password VARCHAR(255),         -- Plain text (admin viewing only)
    password_generated_at TIMESTAMP,    -- When password was generated
    email_verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Password Storage Strategy

| Field | Type | Purpose |
|-------|------|---------|
| `password` | Bcrypt Hash | Used for login authentication |
| `temp_password` | Plain Text | Allows admin to view/recover passwords |
| `password_generated_at` | Timestamp | Track when password was created |

**Security Notes:**
- Plain text password is **only visible to admins** in secure admin panels
- Normal users cannot see their own plain text password
- Password should be changed by users after first login (recommended)

---

## Viewing Generated Passwords

### Admin Panel - Credentials Page

**File:** `resources/views/admin/credentials.blade.php`

```blade
<!-- Student Password Display -->
<input type="password" class="form-control font-monospace" 
       value="{{ $student->user->temp_password ?? 'Not Set' }}" 
       id="password-{{ $student->id }}" readonly>

<small class="text-muted">
    Generated: {{ $student->user->password_generated_at 
        ? \Carbon\Carbon::parse($student->user->password_generated_at)->diffForHumans() 
        : 'N/A' }}
</small>
```

### Principal Dashboard

**File:** `resources/views/dashboard/principal.blade.php`

```blade
<!-- Teacher Password Display -->
<input type="password" class="form-control font-monospace" 
       value="{{ $user->temp_password ?? 'Not Set' }}" 
       id="password-{{ $user->id }}" readonly>

<small class="text-muted">
    {{ $user->password_generated_at 
        ? \Carbon\Carbon::parse($user->password_generated_at)->diffForHumans() 
        : 'N/A' }}
</small>
```

---

## Reset Password Logic

### Admin Can Reset Passwords

**File:** `app/Http/Controllers/Web/AdminController.php`

```php
public function resetPassword(Request $request, $userId)
{
    $user = User::findOrFail($userId);
    
    // Generate new password
    $newPassword = PasswordHelper::generate(10);
    
    // Update user
    $user->update([
        'password' => Hash::make($newPassword),
        'temp_password' => $newPassword,
        'password_generated_at' => now(),
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Password reset successfully',
        'new_password' => $newPassword,
    ]);
}
```

---

## Usage Examples

### Creating a Teacher (Auto-generated Password)

```bash
POST /dashboard/teachers/store
{
    "name": "John Doe",
    "email": "john.doe@schoolerp.com",
    "department_id": 1,
    "phone": "1234567890"
    // No password provided - auto-generated
}
```

**Result:**
- Password: `aB3xY9kL2m` (random 10 chars)
- Admin can view in credentials panel

### Creating a Teacher (Custom Password)

```bash
POST /dashboard/teachers/store
{
    "name": "Jane Smith",
    "email": "jane.smith@schoolerp.com",
    "password": "MySecure123",
    "password_confirmation": "MySecure123"
}
```

**Result:**
- Password: `MySecure123` (admin-provided)
- Still stored in `temp_password` for viewing

### Creating a Student

```bash
POST /dashboard/students/store
{
    "first_name": "Alice",
    "last_name": "Johnson",
    "program_id": 1,
    "division_id": 2,
    "academic_session_id": 1,
    "academic_year": "2025-2026",
    "student_status": "active"
    // Password always auto-generated
}
```

**Result:**
- Email: `alice.johnson{ID}@student.schoolerp.com` (if not provided)
- Password: `xK7mN2pQ9r` (random 10 chars)

---

## Related Files

| File | Purpose |
|------|---------|
| `app/Helpers/PasswordHelper.php` | Password generation logic |
| `app/Http/Controllers/Web/TeacherController.php` | Teacher creation |
| `app/Http/Controllers/Web/StudentController.php` | Student creation |
| `app/Http/Controllers/Web/AdminController.php` | Password reset |
| `resources/views/admin/credentials.blade.php` | View credentials |
| `resources/views/dashboard/principal.blade.php` | Principal view |

---

## Best Practices

1. **First Login:** Require users to change password on first login
2. **Email Notification:** In production, email passwords to users (not just store in admin)
3. **Audit Trail:** Log password resets for security auditing
4. **Expiry:** Consider password expiry policies for sensitive roles
5. **Strength:** Current passwords are 10 chars alphanumeric; consider adding symbols for stronger passwords

---

## Future Enhancements

- [ ] Add option to send password via email/SMS
- [ ] Implement "force password change on first login"
- [ ] Add password strength meter for custom passwords
- [ ] Log all password view/reset actions
- [ ] Add optional password expiry settings
- [ ] Support for password policies (min length, complexity)
