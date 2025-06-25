<?php
/**
 * Hulpfuncties voor StudyMate Event Manager
 * 
 * Dit bestand bevat alle algemene functies die in de applicatie worden gebruikt,
 * zoals inlogcontroles, invoervalidatie, beveiliging en systeemmeldingen.
 * Deze functies worden door meerdere pagina's binnen de applicatie gebruikt.
 */

// Start de sessie voor gebruikersauthenticatie en flash-berichten
session_start();
// Laad de databaseverbinding
require_once 'db_connect.php';

/**
 * Controleert of een gebruiker momenteel is ingelogd
 * 
 * Deze functie kijkt in de sessie of er een gebruiker-ID aanwezig is.
 * Als er een ID gevonden wordt, betekent dit dat iemand is ingelogd.
 * De sessie werkt als een tijdelijk geheugen dat bewaard blijft terwijl 
 * de gebruiker door de website navigeert.
 * 
 * @return bool - True als de gebruiker is ingelogd, anders False
 */
function isLoggedIn(): bool {
    // Controleert of 'user_id' bestaat in de sessie EN of deze niet leeg is
    // Zo ja, dan is de gebruiker ingelogd en geeft de functie 'true' terug
    // Zo nee, dan is niemand ingelogd en geeft de functie 'false' terug
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vereist dat een gebruiker is ingelogd om de pagina te bekijken
 * 
 * Deze functie beschermt pagina's die alleen voor ingelogde gebruikers bedoeld zijn.
 * Als iemand niet is ingelogd, wordt deze automatisch doorgestuurd naar de inlogpagina.
 * Dit voorkomt dat onbevoegden toegang krijgen tot beveiligde delen van de website.
 */
function requireLogin(): void {
    // Controleert eerst of de gebruiker NIET is ingelogd door de isLoggedIn functie te gebruiken
    if (!isLoggedIn()) {
        // Als de gebruiker niet is ingelogd, stuur dan door naar de inlogpagina (index.php)
        header("Location: index.php");
        // Stop direct met het uitvoeren van de rest van de code
        // Dit is belangrijk voor de veiligheid: er wordt geen enkele code meer uitgevoerd
        exit();
    }
    // Als de gebruiker WEL is ingelogd, gebeurt er niets en gaat de code gewoon verder
}

/**
 * Maakt invoer van gebruikers veilig voor gebruik in de website
 * 
 * Deze functie beschermt tegen hackers die proberen code in te voeren.
 * Alle speciale tekens (zoals < > " ') worden omgezet naar veilige HTML-codes.
 * Ook worden onnodige spaties aan het begin en einde verwijderd.
 * 
 * Gebruik deze functie ALTIJD wanneer je informatie van gebruikers verwerkt!
 * 
 * @param string $data - De tekst die de gebruiker heeft ingevoerd
 * @return string - De opgeschoonde, veilige versie van de invoer
 */
function sanitizeInput(string $data): string {
    // trim() verwijdert spaties aan begin en einde van de tekst
    // htmlspecialchars() zet gevaarlijke tekens om in veilige HTML-codes
    // ENT_QUOTES zorgt ervoor dat zowel dubbele als enkele aanhalingstekens worden omgezet
    // UTF-8 is de tekencodering die we gebruiken voor internationale tekens
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Maakt een veilige, willekeurige code voor wachtwoord-reset links
 * 
 * Deze functie genereert een unieke code die gebruikt wordt in e-mails
 * voor het resetten van wachtwoorden. De code is zeer moeilijk te raden
 * omdat deze volledig willekeurig wordt gemaakt met speciale beveiligingsmethoden.
 * 
 * @return string - Een unieke code van 32 tekens die niet te voorspellen is
 */
function generateResetToken(): string {
    // random_bytes(16) maakt 16 willekeurige bytes aan met cryptografische veiligheid
    // bin2hex zet deze bytes om in een leesbare tekst van hexadecimale tekens (0-9, a-f)
    // Het resultaat is een token van 32 tekens die extreem moeilijk te raden is
    return bin2hex(random_bytes(16));
}

/**
 * Slaat een tijdelijk bericht op om aan de gebruiker te tonen
 * 
 * Deze functie maakt het mogelijk om berichten ('Gelukt!', 'Fout!', etc.)
 * op te slaan die maar één keer getoond worden op de volgende pagina.
 * Dit is handig voor bijvoorbeeld bevestigingen na het opslaan van gegevens.
 * 
 * @param string $type - Soort bericht ('success', 'danger', 'warning', etc.)
 * @param string $message - De tekst van het bericht zelf
 */
function setFlashMessage(string $type, string $message): void {
    // Slaat in de sessie een array op met het type bericht en de boodschap zelf
    // Deze informatie blijft bewaard tot de gebruiker naar een andere pagina gaat
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Haalt een tijdelijk bericht op en maakt het daarna schoon
 * 
 * Wat doet deze functie precies:
 * 1. Hij kijkt of er een kort bericht (flash-bericht) is opgeslagen in de gegevens van de gebruiker
 * 2. Als er een bericht is, haalt hij dit op zodat het kan worden getoond
 * 3. Hij verwijdert meteen dit bericht, zodat het maar één keer wordt getoond
 * 4. Hij geeft het bericht terug aan de pagina die het wil gebruiken
 * 
 * Deze functie wordt gebruikt om berichten te tonen zoals:
 * - "Je evenement is succesvol toegevoegd"
 * - "Je wachtwoord is gewijzigd"
 * - "Er is iets misgegaan bij het verwijderen"
 * 
 * De berichten worden maar één keer getoond, ook als je de pagina daarna ververst.
 * 
 * De functie geeft het bericht terug als het bestaat, anders geeft hij 'null' (niets) terug.
 */
function getFlashMessage(): ?array {
    // Controleer of er een flash-bericht is opgeslagen in de sessie van de gebruiker
    // Een sessie is als een geheugen dat de website gebruikt om dingen te onthouden over een gebruiker
    if (isset($_SESSION['flash'])) {
        // Als er een bericht is, bewaar het tijdelijk in de variabele $flash
        // Zodat we het straks kunnen teruggeven aan de pagina die het wil tonen
        $flash = $_SESSION['flash'];
        
        // Verwijder het bericht direct uit de sessie
        // Dit zorgt ervoor dat als de gebruiker de pagina ververst, het bericht niet opnieuw verschijnt
        // Het is als een zelfvernietigende notitie - eenmaal gelezen, wordt het verwijderd
        unset($_SESSION['flash']);
        
        // Geef het bericht terug aan de pagina die de functie heeft aangeroepen
        // Zodat die pagina het kan tonen aan de gebruiker
        return $flash;
    }
    
    // Als er geen bericht is gevonden, geef dan 'null' terug
    // Dit vertelt de pagina dat er geen bericht is om te tonen
    return null;
}
?>