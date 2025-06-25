<?php
/**
 * Dit script verwijdert een evenement uit de database.
 * Het controleert eerst of de gebruiker is ingelogd en of het evenement bij de gebruiker hoort.
 * Dit voorkomt ongeautoriseerde verwijderingen en verhoogt de beveiliging.
 */

// Laadt de functies en databaseverbinding vanuit functions.php
require_once 'functions.php'; // Zorgt ervoor dat alle benodigde functies en databaseverbinding beschikbaar zijn.

// Controleert of de gebruiker is ingelogd
requireLogin(); // Deze functie stuurt de gebruiker naar de inlogpagina als hij niet is ingelogd.

// Haalt het evenement-ID uit de URL-parameter en valideert het
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Zorgt ervoor dat alleen een geldig geheel getal wordt geaccepteerd.

// Controleert of het ID geldig is
if ($id === false || $id === null) {
    die("Ongeldig evenement-ID."); // Stopt de uitvoering als het ID niet geldig is en toont een foutmelding.
}

// Bereidt een SQL-query voor om het evenement te verwijderen
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?"); 
// De query verwijdert alleen evenementen die bij de ingelogde gebruiker horen. Dit voorkomt dat gebruikers elkaars evenementen verwijderen.

// Voert de query uit met het evenement-ID en gebruikers-ID
$result = $stmt->execute([$id, $_SESSION['user_id']]); 
// De execute-methode voert de query uit en gebruikt de parameters om SQL-injecties te voorkomen.

// Slaat een feedbackbericht op in de sessie
$_SESSION['message'] = $result ? "Evenement succesvol verwijderd!" : "Fout bij het verwijderen."; 
// Als de verwijdering succesvol is, wordt een succesbericht opgeslagen. Anders wordt een foutmelding opgeslagen.

// Stuurt de gebruiker terug naar de dashboardpagina
header("Location: dashboard.php"); 
// De gebruiker wordt doorgestuurd naar de dashboardpagina waar het resultaatbericht wordt getoond.

// Stopt de uitvoering van het script
exit(); // Zorgt ervoor dat er geen verdere code wordt uitgevoerd na de doorverwijzing.
?>
