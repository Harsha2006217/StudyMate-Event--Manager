<?php
/**
 * Hulpfuncties voor StudyMate Event Manager
 * 
 * Dit bestand bevat alle algemene functies die in de applicatie worden gebruikt,
 * zoals inlogcontroles, invoervalidatie, beveiliging en systeemmeldingen.
 */

// Start de sessie voor gebruikersauthenticatie en flash-berichten
session_start(); // Hiermee wordt een sessie gestart zodat gegevens tussen pagina's kunnen worden gedeeld.

// Laad de databaseverbinding
require_once 'db_connect.php'; // Verbindt met de database zodat gegevens kunnen worden opgehaald of opgeslagen.

/**
 * Controleert of een gebruiker momenteel is ingelogd
 * 
 * @return bool - True als de gebruiker is ingelogd, anders False
 */
function isLoggedIn(): bool {
    // Controleert of er een 'user_id' in de sessie staat en of deze niet leeg is.
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vereist dat een gebruiker is ingelogd om de pagina te bekijken
 * 
 * Als de gebruiker niet is ingelogd, wordt deze doorgestuurd naar de inlogpagina.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: index.php"); // Verwijst de gebruiker naar de inlogpagina.
        exit(); // Stopt verdere uitvoering van de code om beveiligingsproblemen te voorkomen.
    }
}

/**
 * Maakt invoer van gebruikers veilig voor gebruik in de website
 * 
 * @param string $data - De tekst die de gebruiker heeft ingevoerd
 * @return string - De opgeschoonde, veilige versie van de invoer
 */
function sanitizeInput(string $data): string {
    // trim() verwijdert spaties aan het begin en einde van de tekst.
    // htmlspecialchars() voorkomt dat speciale tekens zoals < en > worden geïnterpreteerd als HTML.
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Maakt een veilige, willekeurige code voor wachtwoord-reset links
 * 
 * @return string - Een unieke code van 32 tekens
 */
function generateResetToken(): string {
    // random_bytes(16) genereert 16 willekeurige bytes.
    // bin2hex() zet deze bytes om in een hexadecimale string.
    return bin2hex(random_bytes(16));
}

/**
 * Slaat een tijdelijk bericht op om aan de gebruiker te tonen
 * 
 * @param string $type - Soort bericht ('success', 'danger', 'warning', etc.)
 * @param string $message - De tekst van het bericht zelf
 */
function setFlashMessage(string $type, string $message): void {
    // Slaat het bericht op in de sessie zodat het op de volgende pagina kan worden weergegeven.
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Haalt een tijdelijk bericht op en maakt het daarna schoon
 * 
 * @return ?array - Het flash-bericht als array, of null als er geen bericht is
 */
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash']; // Haalt het bericht op uit de sessie.
        unset($_SESSION['flash']); // Verwijdert het bericht uit de sessie zodat het niet opnieuw wordt weergegeven.
        return $flash; // Geeft het bericht terug.
    }
    return null; // Geeft null terug als er geen bericht is.
}
?>
