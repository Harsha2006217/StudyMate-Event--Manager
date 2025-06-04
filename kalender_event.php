<?php
/**
 * Kalenderweergave van Evenementen
 * 
 * Deze pagina toont een visuele kalenderweergave van alle evenementen van de 
 * ingelogde gebruiker, georganiseerd per dag van de huidige maand.
 */

// Laad de benodigde functies en controleer of de gebruiker is ingelogd
require_once 'functions.php';
requireLogin();

// Haal alle evenementen op van de ingelogde gebruiker, gesorteerd op datum
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Kalender</title>
    
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
                    <li class="nav-item"><a class="nav-link active" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina - Kalenderweergave -->
    <section class="container mt-5">
        <h2 class="text-center">Kalender</h2>
        <!-- Container voor de dynamisch gegenereerde kalender -->
        <div id="calendar" class="row"></div>
    </section>
    
    <!-- Voettekst van de pagina -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript-bibliotheken -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    
    <!-- JavaScript voor het dynamisch genereren van de kalender -->
    <script>
        // Zet PHP-array met evenementen om naar JavaScript-array
        const events = <?php echo json_encode($events); ?>;
        
        // Verkrijg referentie naar de kalender container
        const calendar = document.getElementById('calendar');
        
        // Bepaal de huidige datum en het aantal dagen in de huidige maand
        const today = new Date();
        const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();

        // Loop door elke dag van de maand om kalender te genereren
        for (let i = 1; i <= daysInMonth; i++) {
            // Maak een div-element voor elke dag
            const dayDiv = document.createElement('div');
            dayDiv.className = 'col-2 p-2 border';
            dayDiv.innerHTML = `<strong>${i}</strong>`;

            // Controleer voor elke dag of er evenementen zijn
            events.forEach(event => {
                const eventDate = new Date(event.date);
                // Als het evenement op deze dag van de huidige maand valt, voeg het toe
                if (eventDate.getDate() === i && eventDate.getMonth() === today.getMonth()) {
                    const eventDiv = document.createElement('div');
                    eventDiv.className = `event ${event.category}`;
                    eventDiv.textContent = `${event.time} - ${event.title}`;
                    dayDiv.appendChild(eventDiv);
                }
            });

            // Voeg de dag toe aan de kalender
            calendar.appendChild(dayDiv);
            
            // Voeg een mooie animatie toe met GSAP
            gsap.from(dayDiv, { opacity: 0, y: 20, duration: 0.5, delay: i * 0.05 });
        }
    </script>
</body>
</html>