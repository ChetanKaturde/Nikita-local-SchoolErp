<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=schoolerp', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check academic_years
echo "=== academic_years ===\n";
$stmt = $pdo->query('SELECT * FROM academic_years');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

// Check holidays
echo "\n=== holidays ===\n";
$stmt = $pdo->query('SELECT * FROM holidays');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

// Insert academic years manually
echo "\n=== Inserting academic years manually ===\n";
$sql = "INSERT INTO academic_years (session_name, start_date, end_date, is_active, created_at, updated_at) VALUES 
('2025-26', '2025-06-01', '2026-05-31', 1, NOW(), NOW()),
('2024-25', '2024-06-01', '2025-05-31', 0, NOW(), NOW())";
try {
    $pdo->exec($sql);
    echo "Academic years inserted.\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

// Insert holidays manually
echo "\n=== Inserting holidays manually ===\n";
$sql = "INSERT INTO holidays (title, description, start_date, end_date, type, is_recurring, academic_year_id, is_active, created_at, updated_at) VALUES 
('Independence Day', 'National holiday', '2025-08-15', '2025-08-15', 'public_holiday', 0, 1, 1, NOW(), NOW()),
('Diwali Break', 'Diwali vacation', '2025-10-20', '2025-10-26', 'school_holiday', 0, 1, 1, NOW(), NOW()),
('Annual Day', 'School annual function', '2025-12-15', '2025-12-15', 'event', 0, 1, 1, NOW(), NOW())";
try {
    $pdo->exec($sql);
    echo "Holidays inserted.\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

// Verify
echo "\n=== After manual insert ===\n";
echo 'academic_years: '.$pdo->query('SELECT COUNT(*) FROM academic_years')->fetchColumn()."\n";
echo 'holidays: '.$pdo->query('SELECT COUNT(*) FROM holidays')->fetchColumn()."\n";
