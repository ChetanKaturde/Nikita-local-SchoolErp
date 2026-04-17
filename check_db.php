<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307', 'root', '');
$stmt = $pdo->query('SHOW DATABASES');
echo "=== Available Databases ===\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Database']."\n";
}
