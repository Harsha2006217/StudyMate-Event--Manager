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
 * Haalt een opgeslagen flash-bericht op en verwijdert het daarna
 * 
 * Deze functie controleert of er een flash-bericht is opgeslagen in de sessie,
 * geeft het terug als dat zo is, en verwijdert het vervolgens zodat het niet 
 * opnieuw wordt weergegeven bij volgende paginaweergaven.
 * 
 * @return array|null - Het flash-bericht als array (type en message) of null als er geen bericht is
 */
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>