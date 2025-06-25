<?php
/**
 * Dit bestand verzorgt het veilig uitloggen van gebruikers.
 * Het verwijdert de actieve sessie, maakt sessievariabelen leeg en stuurt de gebruiker door naar de inlogpagina.
 */

// Inclusie van het functions.php bestand.
// Dit bestand bevat algemene functies en start automatisch een sessie die nodig is voor het uitlogproces.
require_once 'functions.php'; // Zorgt ervoor dat functies uit functions.php beschikbaar zijn.

// Verwijder de huidige sessie volledig van de server.
// Dit voorkomt dat sessiegegevens blijven bestaan na het uitloggen.
session_destroy(); // Vernietigt de sessie en maakt deze onbruikbaar.

// Maak de $_SESSION array leeg.
// Dit is een extra veiligheidsmaatregel om ervoor te zorgen dat er geen sessiegegevens meer beschikbaar zijn.
$_SESSION = []; // Leegt de sessievariabelen om alle gegevens te verwijderen.

// Stuur de gebruiker door naar de inlogpagina.
// De header() functie stuurt een HTTP-header naar de browser om de gebruiker te redirecten.
header("Location: index.php"); // Verwijst de gebruiker naar de inlogpagina (index.php).

// Stop de uitvoering van het script.
// Dit voorkomt dat er nog code wordt uitgevoerd na de redirect, wat belangrijk is voor de veiligheid.
exit(); // Beëindigt het script om verdere verwerking te voorkomen.
?>
