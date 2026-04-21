<?php
// Quick database connection test
$host = '127.0.0.1';
$port = '3307';
$db = 'schoolerp';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to schoolerp database\n\n";
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in schoolerp database (" . count($tables) . "):\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($tables as $table) {
        // Get row count for each table
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $countStmt->fetchColumn();
        echo sprintf("%-40s %s rows\n", $table, $count);
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "\nMySQL might not be running. Please:\n";
    echo "1. Open XAMPP Control Panel\n";
    echo "2. Click 'Start' next to MySQL\n";
    echo "3. Run this script again\n";
}
?>
