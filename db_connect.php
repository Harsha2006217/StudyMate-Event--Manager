<?php
// Databaseconfiguratie
const DB_HOST = 'localhost';
const DB_NAME = 'studymate_db';
const DB_USER = 'root';
const DB_PASS = '';

try {
    // PDO-verbinding met UTF-8 en foutafhandeling
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    // Log fout naar bestand (in productie) en toon generieke melding
    error_log("Databaseverbinding mislukt: " . $e->getMessage());
    die("Er is een probleem met de database. Probeer het later opnieuw.");
}
?>