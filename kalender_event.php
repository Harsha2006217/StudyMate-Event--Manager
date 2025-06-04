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
    <!-- Deze regels geven informatie aan de browser over hoe de pagina moet worden weergegeven -->
    
    <!-- Deze regel zorgt ervoor dat speciale tekens (zoals é, ö, ç) goed worden weergegeven -->
    <meta charset="UTF-8">
    
    <!-- Deze regel zorgt ervoor dat de pagina goed werkt op mobiele telefoons -->
    <!-- Het past de grootte van de website aan aan de grootte van het scherm -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- De titel die bovenaan in het browsertabblad wordt weergegeven -->
    <title>StudyMate - Kalender</title>
    
    <!-- Hieronder laden we de opmaakbestanden (CSS) in die bepalen hoe de website eruitziet -->
    
    <!-- Dit is ons eigen opmaakbestand waar we speciale stijlen hebben gemaakt voor onze website -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Dit is een extern opmaakbestand (Bootstrap) dat veel kant-en-klare stijlen bevat -->
    <!-- We gebruiken dit om de website er snel mooi uit te laten zien zonder veel eigen code te schrijven -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Dit is de navigatiebalk (menu) bovenaan de pagina -->
    <!-- navbar-dark bg-dark zorgt voor een donkere balk met lichte tekst -->
    <!-- navbar-expand-lg zorgt ervoor dat het menu uitklapt op grote schermen -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Dit is de naam/logo van de website links in de navigatiebalk -->
            <!-- De naam is ook een link naar de startpagina (als je erop klikt) -->
            <!-- navbar-brand is een speciale stijl die de tekst groter en opvallender maakt -->
            <a class="navbar-brand" href="#">StudyMate</a>
            
            <!-- Dit is de knop voor het mobiele menu (hamburger menu) -->
            <!-- Als je op deze knop klikt, opent of sluit het menu op kleine schermen -->
            <!-- data-bs-toggle en data-bs-target zijn speciale gegevens-attributen voor Bootstrap -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Dit is de lijst met menu-items die verschijnen als je op het mobiele menu klikt -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Elke regel hieronder is een menu-item -->
                    <!-- class="nav-item" is een speciale stijl voor de menu-items -->
                    <!-- class="nav-link" is een speciale stijl voor de links in het menu -->
                    <!-- href="dashboard.php" is de link naar de startpagina van de gebruiker -->
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <!-- href="add_event.php" is de link naar de pagina om een nieuw evenement toe te voegen -->
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <!-- href="kalender_event.php" is de link naar deze kalenderpagina -->
                    <!-- class="active" zorgt ervoor dat dit menu-item gemarkeerd is als je op deze pagina bent -->
                    <li class="nav-item"><a class="nav-link active" href="kalender_event.php">Kalender</a></li>
                    <!-- href="logout.php" is de link om uit te loggen -->
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Dit is het hoofdgedeelte van de pagina waar de kalender wordt weergegeven -->
    <!-- container zorgt voor een nette uitlijning en ruimte rondom de inhoud -->
    <!-- mt-5 voegt een marge van 5 eenheden toe aan de bovenkant (top) van de container -->
    <section class="container mt-5">
        <!-- Dit is de titel van de pagina, groot en gecentreerd -->
        <h2 class="text-center">Kalender</h2>
        
        <!-- Dit is de container waar de kalender in komt te staan -->
        <!-- row zorgt voor een rasterindeling (grid) voor de kalenderdagen -->
        <div id="calendar" class="row"></div>
    </section>
    
    <!-- Dit is de voettekst onderaan de pagina -->
    <!-- bg-dark maakt de achtergrond donker -->
    <!-- text-white maakt de tekst wit -->
    <!-- text-center centreert de tekst -->
    <!-- py-3 voegt wat ruimte (padding) toe boven en onder de tekst -->
    <!-- mt-5 voegt een marge van 5 eenheden toe aan de bovenkant (top) van de voettekst -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <!-- Dit is het copyright-bericht onderaan de pagina -->
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- Hieronder laden we de JavaScript-bestanden die nodig zijn voor de website om te functioneren -->
    
    <!-- Dit is een extern JavaScript-bestand (Bootstrap) dat veel kant-en-klare functionaliteiten bevat -->
    <!-- We gebruiken dit voor dingen zoals het mobiele menu en andere interactieve onderdelen -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dit is een extern JavaScript-bestand (GSAP) voor geavanceerde animaties -->
    <!-- We gebruiken dit om mooie animaties te maken voor de kalender en andere onderdelen -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    
    <!-- Dit is ons eigen JavaScript-gedeelte waar we de kalender dynamisch maken met code -->
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