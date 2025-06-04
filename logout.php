<?php
/**
 * Uitlogscript voor StudyMate Event Manager
 * 
 * Dit bestand verwerkt het uitlogproces van gebruikers door de sessie te 
 * beëindigen en de gebruiker terug te sturen naar de inlogpagina. Wanneer dit 
 * script wordt aangeroepen, zal de gebruiker direct worden uitgelogd.
 */

// Laad de benodigde functies (dit initieert ook de sessie)
require_once 'functions.php';

// Vernietig de sessie en alle opgeslagen sessiegegevens
session_destroy();  // Verwijdert alle sessiedata van de server

// Leeg de sessie-array voor extra zekerheid
// Dit zorgt ervoor dat zelfs als session_destroy() om een of andere reden faalt, 
// de sessiegegevens nog steeds worden verwijderd uit het geheugen van het script
$_SESSION = []; 

// Stuur de gebruiker door naar de inlogpagina
// Na uitloggen moet de gebruiker opnieuw inloggen om toegang te krijgen
header("Location: index.php");

// Stop de uitvoering van het script
// Dit voorkomt dat er code na de redirect wordt uitgevoerd
exit();
?>