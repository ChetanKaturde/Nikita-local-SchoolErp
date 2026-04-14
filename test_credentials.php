<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING USER CREDENTIALS ===\n\n";

// Test specific known passwords
$testPasswords = ['admin123', 'password', 'password123', 'librarian123'];

$users = DB::table('users')
    ->select('id', 'name', 'email', 'password', 'temp_password')
    ->whereIn('email', [
        'admin@schoolerp.com',
        'teacher@schoolerp.com', 
        'accountant@schoolerp.com',
        'principal@schoolerp.com',
        'librarian@schoolerp.com',
        'superadmin@schoolerp.com'
    ])
    ->get();

foreach($users as $user) {
    echo "\nUser: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Temp Password: " . ($user->temp_password ?? 'NULL') . "\n";
    
    // Test if temp_password matches
    if($user->temp_password) {
        $match = password_verify($user->temp_password, $user->password);
        echo "Temp password matches hashed password: " . ($match ? '✓ YES' : '✗ NO') . "\n";
    }
    
    // Test common passwords
    foreach($testPasswords as $testPass) {
        $match = password_verify($testPass, $user->password);
        if($match) {
            echo "✓ Actual password is: {$testPass}\n";
        }
    }
    
    echo str_repeat('-', 60) . "\n";
}

echo "\n=== DONE ===\n";
