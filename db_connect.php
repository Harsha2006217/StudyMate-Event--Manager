<?php
/**
 * Dit script maakt verbinding met de MySQL database voor StudyMate Event Manager.
 * Het is essentieel voor het ophalen en opslaan van gegevens in de applicatie.
 */

// Definieer de database configuratie constanten
const DB_HOST = 'localhost';    // Hostnaam van de database server (meestal 'localhost' voor lokale servers)
const DB_NAME = 'studymate_db'; // Naam van de database die gebruikt wordt
const DB_USER = 'root';         // Gebruikersnaam voor toegang tot de database
const DB_PASS = '';             // Wachtwoord voor toegang tot de database (standaard leeg bij XAMPP)

try {
    // Maak een nieuwe PDO-verbinding
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        // ↑ Connection string: bevat het type database (mysql), hostnaam, database naam en tekenset (utf8mb4 voor speciale tekens)
        DB_USER,  // Gebruikersnaam voor de database
        DB_PASS,  // Wachtwoord voor de database
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] // Stel PDO in om fouten als exceptions te behandelen
    );
    // ↑ Als deze code succesvol is, is er een actieve verbinding met de database via de $pdo variabele.
} catch (PDOException $e) {
    // Foutafhandeling als de verbinding mislukt
    error_log("Database-verbinding mislukt: " . $e->getMessage());
    // ↑ Log de technische foutmelding in het error log bestand voor ontwikkelaars

    die("Er is een probleem met de database. Probeer het later opnieuw.");
    // ↑ Toon een gebruiksvriendelijke foutmelding aan de gebruiker en stop verdere uitvoering van de code
}

// De variabele $pdo kan nu gebruikt worden in andere bestanden voor database acties
?>
