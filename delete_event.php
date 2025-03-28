<?php
require_once 'functions.php';
requireLogin();

// Haal event-ID op en valideer
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    die("Ongeldig evenement-ID.");
}

// Verwijder het evenement
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
$result = $stmt->execute([$id, $_SESSION['user_id']]);

// Redirect met succesmelding via sessie
$_SESSION['message'] = $result ? "Evenement succesvol verwijderd!" : "Fout bij het verwijderen.";
header("Location: dashboard.php");
exit();
?>