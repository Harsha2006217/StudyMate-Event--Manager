<?php
/**
 * Herinneringen-pagina - Hier zie je berichten over je aankomende activiteiten
 * 
 * Deze pagina heeft een simpel doel:
 * 1. Het haalt alle toekomstige evenementen op waar je een herinnering voor hebt ingesteld
 * 2. Het toont deze evenementen in een lijst, zodat je weet wat er binnenkort gaat gebeuren
 * 3. Zo mis je nooit meer een belangrijke afspraak of gebeurtenis
 */

// Stap 1: Controleren of je wel bent ingelogd
// We laden eerst alle hulpfuncties in die we nodig hebben
require_once 'functions.php';
// Deze regel controleert of je bent ingelogd, anders word je teruggestuurd naar de inlogpagina
// Dit is belangrijk zodat alleen jij je eigen herinneringen kunt zien
requireLogin();

// Stap 2: Ophalen van jouw evenementen met herinneringen
// We maken een zoekopdracht (query) voor de database om alleen bepaalde evenementen te vinden:
// - Alleen JOUW evenementen (WHERE user_id = ?)
// - Alleen evenementen waarvoor een herinnering is ingesteld (AND reminder = 1)
// - Alleen evenementen die vandaag of later plaatsvinden (AND date >= CURDATE())
// - Gesorteerd op datum en tijd, zodat de eerstvolgende bovenaan staan
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? AND reminder = 1 AND date >= CURDATE() ORDER BY date, time");

// Nu voeren we de zoekopdracht uit
// We vullen op de plaats van ? jouw gebruikers-ID in (opgeslagen toen je inlogde)
$stmt->execute([$_SESSION['user_id']]);

// Hier halen we alle gevonden evenementen op en zetten ze in de variabele $events
// Ze komen binnen als een lijst met alle informatie over elk evenement
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stap 3: Voorbereiden van de gegevens voor weergave op het scherm
// We maken een lege lijst waar we alle herinneringen in gaan zetten
$notifications = [];

// We doorlopen elk gevonden evenement één voor één
foreach ($events as $event) {
    // Voor elk evenement voegen we een nieuwe herinnering toe aan onze lijst
    // We maken hierbij een nieuw "pakketje" met alleen de informatie die we willen tonen:
    $notifications[] = [
        // De titel van het evenement (beveiligd tegen schadelijke code met htmlspecialchars)
        'title' => htmlspecialchars($event['title']),
        // De datum van het evenement
        'date' => $event['date'],
        // De tijd van het evenement
        'time' => $event['time'],
        // Hoelang van tevoren je herinnerd wilt worden (bijv. "5 minuten ervoor")
        'reminder_time' => $event['reminder_time']
    ];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Notificaties</title>
    
    <!-- CSS-bestanden voor styling -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk bovenaan de pagina -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo/merknaam -->
            <a class="navbar-brand" href="#">StudyMate</a>
            
            <!-- Hamburger menu voor mobiele weergave -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigatie-items -->
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
    
    <!-- Hoofdinhoud van de pagina - Lijst met notificaties -->
    <section class="container mt-5">
        <h2 class="text-center">Notificaties</h2>
        
        <!-- Controleer of er notificaties zijn; toon een bericht als er geen zijn -->
        <?php if (empty($notifications)): ?>
            <p class="text-center text-muted">Geen notificaties beschikbaar.</p>
        <?php else: ?>
            <!-- Lijst met notificaties als ze beschikbaar zijn -->
            <ul class="list-group">
                <?php foreach ($notifications as $note): ?>
                    <li class="list-group-item">
                        Herinnering: <strong><?php echo $note['title']; ?></strong> op <?php echo $note['date']; ?> om <?php echo $note['time']; ?> (<?php echo $note['reminder_time']; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    
    <!-- Voettekst van de pagina -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript-bestanden voor interactiviteit -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>