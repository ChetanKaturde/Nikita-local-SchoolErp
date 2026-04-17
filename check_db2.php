<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=lemmecode', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SHOW TABLES');
    echo "=== Tables in lemmecode ===\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo current($row)."\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
