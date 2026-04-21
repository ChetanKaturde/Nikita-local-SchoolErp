<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=schoolerp', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check table structure
$tables = ['academic_years', 'holidays'];
foreach ($tables as $table) {
    echo "=== $table structure ===\n";
    $stmt = $pdo->query("DESCRIBE $table");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }
    echo "\n";
}

// Check what columns are in the INSERT statement
$sql = file_get_contents('database/final_working_data.sql');
$pos = strpos($sql, 'INSERT INTO academic_years');
if ($pos !== false) {
    echo "=== academic_years INSERT snippet ===\n";
    echo substr($sql, $pos, 500)."\n";
}
