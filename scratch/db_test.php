<?php
$host = '127.0.0.1';
$port = '3306';
$db   = 'newpbl6';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    echo "Connection to MySQL successful!\n";
    
    $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
    if ($stmt->fetch()) {
        echo "Database '$db' exists.\n";
    } else {
        echo "Database '$db' does NOT exist.\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
