<?php
/**
 * Logout Functionaliteit - StudyMate Event Manager
 * 
 * Dit bestand verzorgt het veilig uitloggen van gebruikers door:
 * - De actieve sessie volledig te verwijderen
 * - Alle sessievariabelen leeg te maken
 * - De gebruiker door te sturen naar de inlogpagina
 */

// Inclusie van het functions.php bestand voor toegang tot algemene functies
// Dit bestand start ook automatisch een sessie die we nodig hebben om uit te loggen
require_once 'functions.php';

// Verwijder de huidige sessie volledig van de server
// Dit is de eerste cruciale stap in het uitlogproces
session_destroy();

// Maak voor de veiligheid ook de $_SESSION array leeg
// Dit zorgt ervoor dat er geen sessiegegevens meer beschikbaar zijn in het huidige script
$_SESSION = []; 

// Stuur de gebruiker door naar de inlogpagina (index.php)
// De header() functie stuurt een HTTP header die de browser vertelt om naar een andere pagina te gaan
header("Location: index.php");

// Stop de uitvoering van het script
// Dit voorkomt dat er nog code wordt uitgevoerd na de doorverwijzing
// en is een belangrijke veiligheidsmaatregel
exit();
?>
