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
 * Deze functie controleert of er een geldige user_id aanwezig is in de sessie,
 * wat aangeeft dat een gebruiker succesvol is ingelogd.
 * 
 * @return bool - True als de gebruiker is ingelogd, anders False
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vereist dat een gebruiker is ingelogd om de pagina te bekijken
 * 
 * Deze functie stuurt de gebruiker door naar de inlogpagina als ze niet 
 * zijn ingelogd. Dit beveiligt pagina's die alleen voor ingelogde gebruikers
 * toegankelijk mogen zijn.
 * 
 * @return void - Functie geeft niets terug, maar kan de gebruiker doorsturen
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

/**
 * Valideert en reinigt gebruikersinvoer voor veilige verwerking
 * 
 * Deze functie voorkomt XSS-aanvallen (Cross-Site Scripting) door speciale
 * tekens om te zetten naar HTML-entiteiten en overbodige spaties te verwijderen.
 * Altijd gebruiken bij het verwerken van gebruikersinvoer!
 * 
 * @param string $data - De ruwe gebruikersinvoer die moet worden opgeschoond
 * @return string - De opgeschoonde en veilige invoergegevens
 */
function sanitizeInput(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Genereert een veilige, willekeurige token voor wachtwoordresets
 * 
 * Deze functie creëert een unieke, cryptografisch veilige token die wordt
 * gebruikt voor het proces van wachtwoordreset. De token is 32 tekens lang
 * en bestaat uit hexadecimale tekens.
 * 
 * @return string - Een unieke token voor wachtwoordreset
 */
function generateResetToken(): string {
    return bin2hex(random_bytes(16));
}

/**
 * Maakt een tijdelijke melding aan die één keer aan de gebruiker wordt getoond
 * 
 * Deze functie slaat een bericht op in de sessie dat op de volgende pagina
 * kan worden weergegeven en daarna automatisch wordt verwijderd. Handig voor
 * bevestigings- of foutmeldingen na formulierinzendingen.
 * 
 * @param string $type - Het type bericht ('success', 'danger', 'warning', etc.)
 * @param string $message - De inhoud van het bericht dat moet worden getoond
 * @return void - Functie slaat het bericht op maar geeft niets terug
 */
function setFlashMessage(string $type, string $message): void {
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