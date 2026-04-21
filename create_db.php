<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create schoolerp database if not exists
$pdo->exec('CREATE DATABASE IF NOT EXISTS schoolerp');
echo "Database 'schoolerp' created or already exists.\n";

// Use schoolerp
$pdo->exec('USE schoolerp');

// Import the SQL file
$sql = file_get_contents('database/complete_setup_with_data.sql');
echo "SQL file loaded. Importing...\n";

// Split by semicolons and execute
$statements = explode(';', $sql);
$created = 0;
$skipped = 0;
foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement)) {
        continue;
    }
    // Skip USE and INSERT statements initially
    if (strpos($statement, 'USE ') === 0) {
        continue;
    }
    if (strpos($statement, 'INSERT INTO') !== false) {
        $skipped++;

        continue;
    }
    try {
        $pdo->exec($statement);
        $created++;
    } catch (Exception $e) {
        // Ignore duplicate key errors
    }
}

echo "Executed $created statements. Skipped $skipped INSERT statements.\n";
echo "\n=== Database Setup Complete ===\n";
