<?php
/**
 * Kalenderpagina - Hier zie je al je evenementen in een maandoverzicht
 * 
 * Dit is de pagina waar je al je afspraken en evenementen in een kalender kunt zien.
 * Het laat alle evenementen zien die bij de huidige maand horen en toont ze op de juiste dag.
 * Zo krijg je een duidelijk overzicht van wat je te doen hebt en wanneer.
 */

// Eerst controleren we of je wel bent ingelogd (anders mag je deze pagina niet zien)
// We laden alle hulpfuncties in die we nodig hebben met require_once
require_once 'functions.php';
// Deze regel zorgt ervoor dat alleen ingelogde gebruikers de kalender kunnen bekijken
// Als je niet bent ingelogd, word je teruggestuurd naar de inlogpagina
requireLogin();

// Nu halen we alle evenementen op die bij jouw account horen
// Stap 1: We maken een veilige database-opdracht (query) klaar
// De ? is een veilige plaatshouder voor je gebruikers-ID
// ORDER BY date zorgt ervoor dat de evenementen op datum worden gesorteerd
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date");

// Stap 2: We voeren de opdracht uit en vullen jouw gebruikers-ID in op de plaats van ?
// $_SESSION['user_id'] is jouw ID-nummer dat is opgeslagen toen je inlogde
$stmt->execute([$_SESSION['user_id']]);

// Stap 3: We halen alle gevonden evenementen op en zetten ze in de variabele $events
// FETCH_ASSOC betekent dat we de gegevens krijgen als een lijst met namen en waarden
// Dit is handig omdat we later de gegevens kunnen opvragen via namen zoals $event['title']
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Metadata voor goede weergave en karaktercodering -->
    <meta charset="UTF-8">
    
    <!-- Maakt de webpagina responsief voor verschillende schermformaten -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>StudyMate - Kalender</title>
    
    <!-- Eigen stylesheet voor aangepaste opmaak -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Bootstrap CSS voor layout en standaard stijlen -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk met menuopties voor de gebruiker -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo/merknaam als link naar de hoofdpagina -->
            <a class="navbar-brand" href="#">StudyMate</a>
            
            <!-- Hamburger menu knop voor mobiele weergave -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigatiemenu met links naar verschillende pagina's -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Menu-items met links naar andere pagina's -->
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link active" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdgedeelte: kalenderweergave container -->
    <section class="container mt-5">
        <!-- Titel van de kalenderpagina -->
        <h2 class="text-center">Kalender</h2>
        
        <!-- Container waarin de kalender dynamisch wordt gegenereerd via JavaScript -->
        <div id="calendar" class="row"></div>
    </section>
    
    <!-- Footer met copyright informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- Bootstrap JavaScript voor interactieve functies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- GSAP bibliotheek voor animatie-effecten -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    
    <!-- JavaScript code voor het genereren van de kalender -->
    <script>
        // Zet PHP-array met evenementen om naar JavaScript-array voor gebruik in de browser
        // json_encode zet de PHP-gegevens om naar JSON-formaat dat JavaScript kan begrijpen
        const events = <?php echo json_encode($events); ?>;
        
        // Selecteer het HTML-element waar de kalender in moet komen
        const calendar = document.getElementById('calendar');
        
        // Maak een object aan met de huidige datum (vandaag)
        // Dit wordt gebruikt om de juiste maand weer te geven
        const today = new Date();
        
        // Bereken hoeveel dagen de huidige maand heeft
        // We maken een datum voor de 0e dag van de volgende maand (wat eigenlijk de laatste dag van de huidige maand is)
        const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();

        // Loop door alle dagen van de maand om de kalender op te bouwen
        for (let i = 1; i <= daysInMonth; i++) {
            // Maak voor elke dag een apart HTML-element aan
            const dayDiv = document.createElement('div');
            // Geef dit element CSS-klassen voor opmaak (Bootstrap grid-systeem)
            dayDiv.className = 'col-2 p-2 border';
            // Zet het dagnummer in het element als dikgedrukte tekst
            dayDiv.innerHTML = `<strong>${i}</strong>`;

            // Doorzoek alle evenementen om te kijken welke op deze dag plaatsvinden
            events.forEach(event => {
                // Maak een Date-object van de evenementdatum voor vergelijking
                const eventDate = new Date(event.date);
                
                // Controleer of het evenement op de huidige dag én in de huidige maand valt
                if (eventDate.getDate() === i && eventDate.getMonth() === today.getMonth()) {
                    // Maak een nieuw element voor dit evenement
                    const eventDiv = document.createElement('div');
                    // Voeg CSS-klassen toe voor opmaak, inclusief categorie-specifieke kleur
                    eventDiv.className = `event ${event.category}`;
                    // Toon tijd en titel van het evenement
                    eventDiv.textContent = `${event.time} - ${event.title}`;
                    // Voeg het evenement toe aan de dag
                    dayDiv.appendChild(eventDiv);
                }
            });

            // Voeg de complete dag toe aan de kalender
            calendar.appendChild(dayDiv);
            
            // Voeg een vloeiende verschijningsanimatie toe met GSAP
            // Elke dag verschijnt met een kleine vertraging na de vorige (i * 0.05)
            gsap.from(dayDiv, { opacity: 0, y: 20, duration: 0.5, delay: i * 0.05 });
        }
    </script>
</body>
</html>