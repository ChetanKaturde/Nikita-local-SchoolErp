<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Mail\SendCredentials;
use Illuminate\Support\Facades\Mail;

echo "\n=== USERS WITH TEMP PASSWORDS ===\n\n";

$users = User::whereNotNull('temp_password')->limit(10)->get();

if ($users->isEmpty()) {
    echo "No users found with temp passwords.\n";
    echo "You need to reset passwords first before sending credentials.\n\n";
    exit;
}

echo "Found " . $users->count() . " users with temp passwords:\n\n";
echo str_pad("ID", 5) . str_pad("Name", 30) . str_pad("Email", 45) . "Password\n";
echo str_repeat("-", 100) . "\n";

foreach ($users as $user) {
    echo str_pad($user->id, 5) . 
         str_pad($user->name, 30) . 
         str_pad($user->email, 45) . 
         ($user->temp_password ?: 'Not Set') . "\n";
}

echo "\n\n=== MAIL CONFIGURATION ===\n\n";
$mailer = config('mail.default');
$smtpHost = config('mail.mailers.smtp.host');
$smtpUsername = config('mail.mailers.smtp.username');
$fromAddress = config('mail.from.address');

echo "Mailer: $mailer\n";
echo "SMTP Host: $smtpHost\n";
echo "SMTP Username: $smtpUsername\n";
echo "From Address: $fromAddress\n\n";

if (empty($smtpUsername) || empty(config('mail.mailers.smtp.password'))) {
    echo "⚠️  WARNING: SMTP credentials are not configured in .env!\n";
    echo "   Please update .env with your Outlook email and app password:\n";
    echo "   - MAIL_USERNAME=your-email@outlook.com\n";
    echo "   - MAIL_PASSWORD=your-app-password\n";
    echo "   - MAIL_FROM_ADDRESS=your-email@outlook.com\n\n";
    echo "   Then run: php artisan config:clear\n\n";
    exit;
}

echo "\n=== SEND CREDENTIALS ===\n\n";
echo "Which user would you like to send credentials to?\n";
echo "Enter User ID (or 'all' to send to all users above): ";

$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));

if (strtolower($input) === 'all') {
    echo "\nSending credentials to all " . $users->count() . " users...\n\n";
    
    $sent = 0;
    $failed = 0;
    
    foreach ($users as $user) {
        try {
            $userType = $user->roles()->first()?->name ?? 'user';
            
            Mail::to($user->email)->send(
                new SendCredentials($user, $user->temp_password, $userType)
            );
            
            echo "✅ Sent to: {$user->name} ({$user->email})\n";
            $sent++;
        } catch (\Exception $e) {
            echo "❌ Failed: {$user->name} ({$user->email}) - " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "\n\n=== SUMMARY ===\n";
    echo "Total: " . $users->count() . "\n";
    echo "Sent: $sent\n";
    echo "Failed: $failed\n\n";
    
} elseif (is_numeric($input)) {
    $user = User::find($input);
    
    if (!$user) {
        echo "User not found!\n";
        exit;
    }
    
    if (empty($user->temp_password)) {
        echo "This user doesn't have a temporary password. Reset their password first.\n";
        exit;
    }
    
    echo "\nSending credentials to: {$user->name} ({$user->email})\n\n";
    
    try {
        $userType = $user->roles()->first()?->name ?? 'user';
        
        Mail::to($user->email)->send(
            new SendCredentials($user, $user->temp_password, $userType)
        );
        
        echo "✅ Successfully sent credentials to {$user->email}\n";
        echo "   Password: {$user->temp_password}\n\n";
    } catch (\Exception $e) {
        echo "❌ Failed to send email: " . $e->getMessage() . "\n\n";
        echo "   Check your SMTP settings in .env file.\n";
        echo "   More details: storage/logs/laravel.log\n\n";
    }
} else {
    echo "Invalid input. Please enter a numeric user ID or 'all'.\n";
}

fclose($handle);
echo "\n";
