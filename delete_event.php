<?php
/**
 * Evenement Verwijderen Script
 * 
 * Dit script verwijdert een evenement uit de database wanneer de gebruiker 
 * op de verwijderknop drukt. Het script beveiligt tegen ongeautoriseerde 
 * verwijderingen door te controleren of de gebruiker is ingelogd en of 
 * het evenement daadwerkelijk van deze gebruiker is.
 */

// Laadt de functions.php file die database-verbinding en hulpfuncties bevat
require_once 'functions.php';

// Controleert of een gebruiker is ingelogd, anders wordt hij/zij doorgestuurd naar de inlogpagina
// Dit voorkomt dat niet-ingelogde bezoekers evenementen kunnen verwijderen
requireLogin();

// Haalt het evenement-ID uit de URL-parameter en controleert of het een geldig geheel getal is
// Dit voorkomt SQL-injectie aanvallen door alleen geldige getallen toe te staan
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Controleert of het ID daadwerkelijk een geldig nummer is
// Als het ID ongeldig is, stopt het script en toont een foutmelding
if ($id === false || $id === null) {
    die("Ongeldig evenement-ID.");
}

// Maakt een veilige SQL-query voor het verwijderen van het evenement
// De voorbereidde statement (prepare) beschermt tegen SQL-injectie
// Het evenement wordt alleen verwijderd als het van de ingelogde gebruiker is
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");

// Voert de query uit met het evenement-ID en het gebruikers-ID als parameters
// Dit zorgt ervoor dat gebruikers alleen hun eigen evenementen kunnen verwijderen
$result = $stmt->execute([$id, $_SESSION['user_id']]);

// Slaat een feedback-bericht op in de sessie om aan de gebruiker te tonen
// Het bericht verschilt afhankelijk van of de verwijdering succesvol was of niet
$_SESSION['message'] = $result ? "Evenement succesvol verwijderd!" : "Fout bij het verwijderen.";

// Stuurt de gebruiker terug naar de dashboard pagina waar het resultaatbericht zal worden getoond
header("Location: dashboard.php");

// Stopt de uitvoering van het script na de doorverwijzing
// Dit voorkomt dat er nog code wordt uitgevoerd nadat de doorverwijzing is gestart
exit();
?>