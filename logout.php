<?php
/**
 * Uitloggen - Dit bestand zorgt ervoor dat je veilig kunt uitloggen
 * 
 * Wat doet deze pagina precies:
 * 1. Het verwijdert al je inloggegevens uit het systeem
 * 2. Het zorgt ervoor dat je niet meer wordt herkend als ingelogde gebruiker
 * 3. Het stuurt je terug naar de inlogpagina
 *
 * Dit is een heel eenvoudige pagina die maar één taak heeft: jou uitloggen.
 * Als je op de "Uitloggen" knop klikt in het menu, kom je op deze pagina terecht.
 */

// We beginnen met het inladen van alle hulpfuncties die we nodig hebben
// Dit bestand (functions.php) bevat allerlei handige functies die we gebruiken
// Het start ook automatisch een sessie, dat is een soort tijdelijk geheugen waar je inloggegevens zijn opgeslagen
require_once 'functions.php';

// Nu gaan we je uitloggen door de sessie te vernietigen
// Een sessie is waar de website onthoudt wie je bent - door deze te vernietigen, 
// vergeet de website als het ware wie je bent
session_destroy();  // Deze opdracht verwijdert je hele sessie van de server

// Voor de zekerheid maken we ook je sessie-variabelen leeg
// Dit is een extra stap om zeker te weten dat alle inloggegevens weg zijn
// Een lege [] betekent dat we de sessie vervangen door een lege lijst zonder gegevens
$_SESSION = []; 

// Nu je uitgelogd bent, sturen we je terug naar de inlogpagina
// De header-functie vertelt je browser om naar een andere pagina te gaan
// In dit geval sturen we je naar index.php, dat is de inlogpagina
header("Location: index.php");

// Deze regel zorgt ervoor dat de code hierna stopt met uitvoeren
// Dit is belangrijk omdat we willen dat de doorverwijzing naar de inlogpagina
// het laatste is wat gebeurt, zonder dat er nog andere dingen worden gedaan
exit();
?>