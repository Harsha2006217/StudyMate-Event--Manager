<?php
/**
 * Evenement Verwijderen Script
 * 
 * Dit script verwerkt het verwijderen van evenementen door gebruikers.
 * Het controleert of de gebruiker is ingelogd, valideert het evenement-ID,
 * en zorgt ervoor dat gebruikers alleen hun eigen evenementen kunnen verwijderen.
 */

// Laad de benodigde functies en controleer of de gebruiker is ingelogd
require_once 'functions.php';
requireLogin();

// Haal event-ID op uit de URL en valideer of het een geldig geheel getal is
// filter_input voorkomt injecties door het controleren van het datatype
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    // Stop de uitvoering als het ID ongeldig is en toon een foutmelding
    die("Ongeldig evenement-ID.");
}

// Verwijder het evenement uit de database
// De WHERE-clausule met user_id zorgt ervoor dat gebruikers alleen hun eigen evenementen kunnen verwijderen
// Dit is een beveiligingsmaatregel om ongeautoriseerde verwijderingen te voorkomen
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
$result = $stmt->execute([$id, $_SESSION['user_id']]);

// Maak een gepaste melding aan op basis van het resultaat van de verwijdering
// De ternaire operator (?) controleert of $result waar is en kiest de juiste melding
$_SESSION['message'] = $result ? "Evenement succesvol verwijderd!" : "Fout bij het verwijderen.";

// Stuur de gebruiker terug naar het dashboard met de statusmelding
header("Location: dashboard.php");
exit(); // Zorg ervoor dat de code stopt na de redirect om verdere uitvoering te voorkomen
?>