<?php
/**
 * Database Verbinding Script
 * 
 * Dit bestand heeft één belangrijke taak: zorgen dat onze website verbinding kan maken
 * met de database waar alle informatie wordt opgeslagen. Zonder deze verbinding kunnen
 * we geen gegevens opslaan of ophalen (zoals gebruikers of evenementen).
 * 
 * Dit is meestal het eerste bestand dat wordt ingeladen in andere PHP-bestanden
 * omdat de database-verbinding bijna altijd nodig is.
 */

// Hieronder staan de vier belangrijke gegevens die nodig zijn om met de database te verbinden
// We gebruiken constanten (const) omdat deze waarden nooit veranderen tijdens het gebruik
const DB_HOST = 'localhost';    // Dit is de naam of het adres van de computer waar de database staat
                               // 'localhost' betekent dat de database op dezelfde computer staat als de website

const DB_NAME = 'studymate_db'; // Dit is de naam van de database die we gaan gebruiken
                               // Hierin staan tabellen zoals 'users' en 'events'

const DB_USER = 'root';         // Dit is de gebruikersnaam die toegang geeft tot de database
                               // 'root' is de standaard beheerder in XAMPP, maar in echte websites
                               // zou je een andere gebruiker met minder rechten gebruiken

const DB_PASS = '';             // Dit is het wachtwoord voor de database-gebruiker
                               // In XAMPP is dit standaard leeg, maar in echte websites
                               // zou je altijd een sterk wachtwoord gebruiken

try {
    // Nu gaan we proberen verbinding te maken met de database
    // Het try-blok zorgt ervoor dat als er iets mis gaat, we netjes een foutmelding kunnen geven
    
    // Hier maken we de echte verbinding met de database
    $pdo = new PDO(
        // Dit is de volledige informatie voor de database-verbinding:
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        // ↑ Dit geeft aan: gebruik MySQL, op deze computer, deze database, en deze tekenset
        // De tekenset (utf8mb4) zorgt dat ook speciale tekens en emoji's opgeslagen kunnen worden
        
        DB_USER,  // Hier geven we de gebruikersnaam door
        DB_PASS,  // Hier geven we het wachtwoord door
        
        // Deze instelling zorgt ervoor dat fouten duidelijk worden gemeld
        // Als er iets mis gaat met een database-opdracht, krijgen we daar bericht van
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
} catch (PDOException $e) {
    // Dit deel wordt alleen uitgevoerd als er iets mis ging bij het verbinden
    // PDOException is een speciaal soort foutmelding voor database-problemen
    
    // Deze regel schrijft de technische foutmelding naar een logbestand
    // Dit is handig voor de beheerder om problemen op te lossen
    error_log("Database-verbinding mislukt: " . $e->getMessage());
    
    // Deze regel toont een vriendelijke foutmelding aan de gebruiker
    // We laten opzettelijk geen technische details zien om veiligheidsredenen
    // De functie 'die()' stopt de uitvoering van de website
    die("Er is een probleem met de database. Probeer het later opnieuw.");
}

// Als alles goed is gegaan, is de variabele $pdo nu klaar voor gebruik
// Deze variabele wordt gebruikt in andere bestanden om gegevens op te halen of op te slaan
// Bijvoorbeeld voor het inloggen van gebruikers of het opslaan van evenementen
?>