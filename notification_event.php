<?php
/**
 * Herinneringen-pagina - Hier zie je berichten over je aankomende activiteiten.
 * Deze pagina haalt toekomstige evenementen op waarvoor herinneringen zijn ingesteld
 * en toont ze in een overzichtelijke lijst.
 */

// Stap 1: Controleren of de gebruiker is ingelogd
require_once 'functions.php'; // Laadt functies die nodig zijn voor de applicatie
requireLogin(); // Controleert of de gebruiker is ingelogd, anders wordt hij doorgestuurd naar de inlogpagina

// Stap 2: Ophalen van evenementen met herinneringen
$stmt = $pdo->prepare(
    "SELECT * FROM events 
    WHERE user_id = ? AND reminder = 1 AND date >= CURDATE() 
    ORDER BY date, time"
);
// Hier wordt een SQL-query voorbereid om veilige gegevens uit de database te halen.
// De query selecteert alleen evenementen van de ingelogde gebruiker met een herinnering
// en die vanaf vandaag plaatsvinden. De resultaten worden gesorteerd op datum en tijd.

$stmt->execute([$_SESSION['user_id']]); // Voert de query uit met de gebruikers-ID uit de sessie
$events = $stmt->fetchAll(PDO::FETCH_ASSOC); // Haalt alle resultaten op als een associatieve array

// Stap 3: Voorbereiden van de gegevens voor weergave
$notifications = []; // Een lege array om notificaties op te slaan

foreach ($events as $event) {
    $notifications[] = [
        'title' => htmlspecialchars($event['title']), // Voorkomt XSS-aanvallen door speciale tekens te ontsnappen
        'date' => $event['date'], // Datum van het evenement
        'time' => $event['time'], // Tijd van het evenement
        'reminder_time' => $event['reminder_time'] // Tijdstip van de herinnering
    ];
}
// Hier wordt elk evenement verwerkt en omgezet naar een eenvoudigere structuur
// die geschikt is voor weergave op de pagina.

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8"> <!-- Zorgt ervoor dat de pagina correct wordt weergegeven -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Maakt de pagina responsive -->
    <title>StudyMate - Notificaties</title>
    <link rel="stylesheet" href="style.css"> <!-- Link naar de eigen CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS voor styling -->
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a> <!-- Merknaam -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link active" href="notifications.php">Notificaties</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="container mt-5">
        <h2 class="text-center">Notificaties</h2>
        <?php if (empty($notifications)): ?>
            <p class="text-center text-muted">Geen notificaties beschikbaar.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($notifications as $note): ?>
                    <li class="list-group-item">
                        Herinnering: <strong><?php echo $note['title']; ?></strong> op <?php echo $note['date']; ?> om <?php echo $note['time']; ?> (<?php echo $note['reminder_time']; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <!-- Hier wordt gecontroleerd of er notificaties zijn. Als er geen notificaties zijn,
             wordt een bericht weergegeven. Anders wordt een lijst met notificaties getoond. -->
    </section>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p> <!-- Voettekst -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS -->
    <script src="script.js"></script> <!-- Link naar eigen JavaScript -->
</body>
</html>
