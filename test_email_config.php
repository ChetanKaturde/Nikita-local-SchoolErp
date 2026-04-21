<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== EMAIL CONFIGURATION TEST ===\n\n";

// Check mail configuration
$mailer = config('mail.mailers.smtp');
$fromAddress = config('mail.from.address');
$fromName = config('mail.from.name');

echo "Mail Configuration:\n";
echo "-------------------\n";
echo "Mailer: " . config('mail.default') . "\n";
echo "SMTP Host: " . ($mailer['host'] ?? 'Not configured') . "\n";
echo "SMTP Port: " . ($mailer['port'] ?? 'Not configured') . "\n";
echo "SMTP Username: " . ($mailer['username'] ?? 'Not configured') . "\n";
echo "From Address: " . ($fromAddress ?: 'Not configured') . "\n";
echo "From Name: " . ($fromName ?: 'Not configured') . "\n\n";

// Check if credentials are set
if (empty($mailer['username']) || empty($mailer['password'])) {
    echo "⚠️  WARNING: SMTP credentials are not configured!\n";
    echo "   Please update your .env file with:\n";
    echo "   - MAIL_USERNAME=your-email@outlook.com\n";
    echo "   - MAIL_PASSWORD=your-app-password\n";
    echo "   - MAIL_FROM_ADDRESS=your-email@outlook.com\n\n";
} else {
    echo "✅ SMTP credentials are configured\n\n";
}

// Test sending an email
echo "Would you like to send a test email? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'y') {
    echo "\nEnter recipient email: ";
    $to = trim(fgets($handle));
    
    if (empty($to)) {
        echo "No email provided. Exiting.\n";
        exit;
    }
    
    try {
        $user = new stdClass();
        $user->name = 'Test User';
        $user->email = $to;
        
        \Illuminate\Support\Facades\Mail::to($to)->send(
            new \App\Mail\SendCredentials($user, 'TestPassword123', 'student')
        );
        
        echo "\n✅ Test email sent successfully to: $to\n";
        echo "   Please check your inbox (and spam folder)\n\n";
    } catch (\Exception $e) {
        echo "\n❌ Failed to send email: " . $e->getMessage() . "\n\n";
        echo "   Check your SMTP credentials and try again.\n";
        echo "   More details in: storage/logs/laravel.log\n\n";
    }
} else {
    echo "\nSkipping test email send.\n\n";
}

echo "Next steps:\n";
echo "-----------\n";
echo "1. Update .env with your Outlook credentials\n";
echo "2. Run: php artisan config:clear\n";
echo "3. Login as Admin and test the send credentials feature\n\n";

fclose($handle);
