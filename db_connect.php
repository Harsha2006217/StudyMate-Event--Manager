<?php
/**
 * Database Verbinding Script
 * 
 * Dit bestand maakt verbinding met de MySQL database voor StudyMate Event Manager.
 * Deze verbinding is essentieel voor elke pagina die informatie moet ophalen of opslaan.
 */

// Database configuratie gegevens
// Deze constanten bevatten de inloggegevens voor de database en veranderen niet tijdens het gebruik
const DB_HOST = 'localhost';    // De server waar de database draait (lokaal op deze computer)
const DB_NAME = 'studymate_db'; // Naam van de specifieke database die we willen gebruiken
const DB_USER = 'root';         // Gebruikersnaam voor toegang tot de database (standaard in XAMPP)
const DB_PASS = '';             // Wachtwoord voor database toegang (leeg bij standaard XAMPP installatie)

try {
    // Hier maken we de database verbinding met PDO (PHP Data Objects)
    // PDO is een veilige manier om met databases te communiceren in PHP
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        // ↑ De connection string bevat: type database (mysql), server (localhost), 
        // databasenaam en tekenset (utf8mb4 voor ondersteuning van speciale tekens)
        
        DB_USER,  // Gebruikersnaam voor de database
        DB_PASS,  // Wachtwoord voor de database
        
        // Deze optie zorgt ervoor dat fouten netjes als exceptions worden getoond
        // Handig tijdens ontwikkeling om problemen snel te kunnen opsporen
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    // Als deze code zonder fouten uitgevoerd wordt, hebben we nu een werkende database verbinding
    // De variabele $pdo kan nu gebruikt worden in andere bestanden voor database acties
    
} catch (PDOException $e) {
    // Dit blok vangt database fouten op als de verbinding mislukt
    // Bijvoorbeeld als de database niet bestaat of inloggegevens onjuist zijn
    
    error_log("Database-verbinding mislukt: " . $e->getMessage());
    // ↑ Slaat de technische foutmelding op in het error log bestand
    // Dit is belangrijk voor ontwikkelaars om problemen op te lossen
    
    die("Er is een probleem met de database. Probeer het later opnieuw.");
    // ↑ Toont een gebruiksvriendelijke foutmelding aan de bezoeker
    // En stopt de verdere uitvoering van de code (die() functie)
}

// De $pdo variabele bevat nu een actieve verbinding met de database
// Deze variabele wordt in andere bestanden gebruikt om gegevens op te halen of op te slaan
?>