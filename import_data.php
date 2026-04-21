<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=schoolerp', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Connected to schoolerp database.\n";

// Import data from final_working_data.sql since it should have all the data
$sqlFile = 'database/final_working_data.sql';
if (! file_exists($sqlFile)) {
    $sqlFile = 'database/complete_setup_with_data.sql';
}

$sql = file_get_contents($sqlFile);
echo "Loaded SQL file: $sqlFile\n";

// Split by semicolons and execute INSERT statements
$statements = explode(';', $sql);
$inserted = 0;
$errors = 0;

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement)) {
        continue;
    }

    // Only execute INSERT statements
    if (strpos($statement, 'INSERT INTO') !== false) {
        try {
            $pdo->exec($statement);
            $inserted++;
        } catch (Exception $e) {
            $errors++;
        }
    }
}

echo "Inserted $inserted statements. Errors: $errors.\n";

// Verify data
echo "\n=== Verification ===\n";

$tables = ['departments', 'programs', 'academic_years', 'divisions', 'subjects', 'users', 'holidays'];
foreach ($tables as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "$table: $count rows\n";
    } catch (Exception $e) {
        echo "$table: Error - ".$e->getMessage()."\n";
    }
}

echo "\n=== Test Data Import Complete ===\n";
