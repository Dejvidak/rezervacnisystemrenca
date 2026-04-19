<?php
// db.php – připojení k databázi

$host = '127.0.0.1';
$db   = 'barbershop';  // název databáze
$user = 'root';        // MySQL user
$pass = '';            // heslo – pokud jsi nenastavoval, nech prázdné
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Chyba připojení k databázi: ' . $e->getMessage());
}
