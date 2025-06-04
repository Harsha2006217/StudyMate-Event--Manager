<?php
/**
 * Evenement Verwijderen Script
 * 
 * Dit bestand heeft één taak: een evenement permanent verwijderen uit de database.
 * 
 * Hoe werkt het:
 * 1. Het controleert eerst of de gebruiker is ingelogd
 * 2. Het haalt het ID-nummer van het te verwijderen evenement uit de URL
 * 3. Het controleert of het ID een geldig nummer is
 * 4. Het verwijdert het evenement uit de database, maar alleen als het van de ingelogde gebruiker is
 * 5. Het stuurt de gebruiker terug naar het overzicht met een bericht over het resultaat
 */

// Stap 1: Inladen van hulpbestanden en controleren of de gebruiker is ingelogd
// Dit laadt alle functies in die we nodig hebben
require_once 'functions.php';
// Deze regel controleert of de gebruiker is ingelogd, zo niet, dan wordt hij/zij naar de inlogpagina gestuurd
// Dit is belangrijk zodat alleen ingelogde gebruikers evenementen kunnen verwijderen
requireLogin();

// Stap 2: Het ID van het evenement ophalen uit de URL en controleren
// In de URL staat bijvoorbeeld "delete_event.php?id=5" waarbij 5 het evenement-ID is
// filter_input zorgt ervoor dat we alleen een geldig nummer accepteren (beveiliging)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Stap 3: Controleren of het ID een geldig nummer is
// Als het ID geen nummer is of niet bestaat in de URL, stoppen we het script
if ($id === false || $id === null) {
    // We stoppen het script en tonen een duidelijke foutmelding aan de gebruiker
    die("Ongeldig evenement-ID.");
}

// Stap 4: Het evenement verwijderen uit de database
// We maken een veilige database-opdracht (query) klaar
// Deze query zegt: "Verwijder het evenement met dit ID, maar alleen als het van deze gebruiker is"
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");

// We voeren de query uit met twee waarden:
// 1. Het ID van het evenement dat we willen verwijderen
// 2. Het ID van de ingelogde gebruiker (uit de sessie)
// De gebruiker kan zo alleen zijn/haar eigen evenementen verwijderen (beveiliging)
$result = $stmt->execute([$id, $_SESSION['user_id']]);

// Stap 5: Een bericht maken dat aan de gebruiker wordt getoond
// We kijken of de verwijdering is gelukt ($result is dan waar/true)
// Als het gelukt is, maken we een succesbericht
// Als het niet gelukt is, maken we een foutmelding
// Het vraagteken ? is een korte manier om te zeggen "als ... dan ... anders ..."
$_SESSION['message'] = $result ? "Evenement succesvol verwijderd!" : "Fout bij het verwijderen.";

// Stap 6: De gebruiker terugsturen naar het dashboard/overzicht
// De header-functie zorgt voor de doorverwijzing naar een andere pagina
header("Location: dashboard.php");

// Stap 7: Het script direct stoppen na de doorverwijzing
// Dit is belangrijk omdat we zeker willen weten dat er niets meer gebeurt na de doorverwijzing
exit();
?>