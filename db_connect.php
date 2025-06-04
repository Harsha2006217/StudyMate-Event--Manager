<?php
/**
 * Database Connectie Script
 * 
 * Dit bestand zorgt voor een veilige verbinding met de MySQL-database
 * voor de StudyMate Event Manager applicatie. Het gebruikt PDO (PHP Data Objects)
 * voor een beveiligde en flexibele databaseverbinding.
 */

// Databaseconfiguratie constanten
// Deze constanten bevatten de benodigde gegevens voor de databaseverbinding
const DB_HOST = 'localhost';    // De databaseserver, meestal localhost voor lokale ontwikkeling
const DB_NAME = 'studymate_db'; // De naam van de database die we willen gebruiken
const DB_USER = 'root';         // De gebruikersnaam voor de database (standaard 'root' in XAMPP)
const DB_PASS = '';             // Het wachtwoord voor databasetoegang (leeg in standaard XAMPP)

try {
    // PDO-verbinding met UTF-8 en foutafhandeling
    // Hier maken we een nieuwe instantie van de PDO-klasse om verbinding te maken
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", // De DSN (Data Source Name) met hostnaam, database en tekenset
        DB_USER,                                                             // Gebruikersnaam
        DB_PASS,                                                             // Wachtwoord
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]                        // Configuratieopties: gooi uitzonderingen bij fouten
    );
    // charset=utf8mb4 zorgt ervoor dat alle tekens (inclusief emoji's) correct worden opgeslagen
    
} catch (PDOException $e) {
    // Log fout naar bestand (in productie) en toon generieke melding
    // Dit voorkomt dat gevoelige databasegegevens zichtbaar worden voor gebruikers
    error_log("Databaseverbinding mislukt: " . $e->getMessage());             // Schrijft de foutmelding naar het PHP error log
    die("Er is een probleem met de database. Probeer het later opnieuw.");    // Stopt de uitvoering en toont een gebruiksvriendelijke melding
}
// De $pdo-variabele is nu beschikbaar voor andere scripts die dit bestand includeren
?>