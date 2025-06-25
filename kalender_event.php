<?php
/**
 * Kalenderpagina - Hier zie je al je evenementen in een maandoverzicht.
 * Deze pagina toont alle evenementen van de huidige maand in een overzichtelijke kalender.
 */

// Controleer of de gebruiker is ingelogd
require_once 'functions.php'; // Laadt de benodigde functies
requireLogin(); // Zorgt ervoor dat alleen ingelogde gebruikers toegang hebben

// Maak verbinding met de database en haal evenementen op
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date"); // Bereidt een veilige SQL-query voor
$stmt->execute([$_SESSION['user_id']]); // Voert de query uit met de gebruikers-ID
$events = $stmt->fetchAll(PDO::FETCH_ASSOC); // Haalt alle evenementen op als een associatief array

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8"> <!-- Zorgt voor correcte karaktercodering -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Maakt de pagina responsief -->
    <title>StudyMate - Kalender</title>
    <link rel="stylesheet" href="style.css"> <!-- Eigen CSS voor opmaak -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
</head>
<body>
    <!-- Navigatiebalk -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a> <!-- Logo -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span> <!-- Hamburger menu -->
            </button>
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

    <!-- Kalenderweergave -->
    <section class="container mt-5">
        <h2 class="text-center">Kalender</h2> <!-- Titel -->
        <div id="calendar" class="row"></div> <!-- Kalendercontainer -->
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p> <!-- Copyright -->
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script> <!-- GSAP voor animaties -->

    <script>
        // Zet PHP-array met evenementen om naar een JavaScript-array
        const events = <?php echo json_encode($events); ?>;

        const calendar = document.getElementById('calendar'); // Selecteert de kalendercontainer
        const today = new Date(); // Huidige datum
        const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate(); // Berekent het aantal dagen in de maand

        // Genereer de kalender
        for (let i = 1; i <= daysInMonth; i++) {
            const dayDiv = document.createElement('div'); // Maak een dag-element
            dayDiv.className = 'col-2 p-2 border'; // Voeg CSS-klassen toe
            dayDiv.innerHTML = `<strong>${i}</strong>`; // Voeg het dagnummer toe

            // Voeg evenementen toe aan de juiste dag
            events.forEach(event => {
                const eventDate = new Date(event.date); // Converteer de evenementdatum
                if (eventDate.getDate() === i && eventDate.getMonth() === today.getMonth()) {
                    const eventDiv = document.createElement('div'); // Maak een evenement-element
                    eventDiv.className = `event ${event.category}`; // Voeg CSS-klassen toe
                    eventDiv.textContent = `${event.time} - ${event.title}`; // Voeg evenementdetails toe
                    dayDiv.appendChild(eventDiv); // Voeg het evenement toe aan de dag
                }
            });

            calendar.appendChild(dayDiv); // Voeg de dag toe aan de kalender
            gsap.from(dayDiv, { opacity: 0, y: 20, duration: 0.5, delay: i * 0.05 }); // Voeg animatie toe
        }
    </script>
</body>
</html>
