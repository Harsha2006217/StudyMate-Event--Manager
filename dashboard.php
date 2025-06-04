<?php
/**
 * Dashboard (overzichtspagina) - De hoofdpagina die gebruikers zien na het inloggen
 * 
 * Dit bestand doet het volgende:
 * 1. Het controleert of de gebruiker is ingelogd
 * 2. Het haalt alle evenementen van de ingelogde gebruiker op
 * 3. Het toont deze evenementen in een netjes overzicht
 * 4. Het geeft opties om evenementen te bewerken of verwijderen
 */

// Deze regel laadt het bestand 'functions.php' met alle handige functies die we nodig hebben
// We gebruiken require_once zodat het bestand precies één keer wordt ingeladen
require_once 'functions.php';

// Deze regel controleert of de gebruiker is ingelogd
// Als de gebruiker niet is ingelogd, wordt hij/zij doorgestuurd naar de inlogpagina
// Dit zorgt ervoor dat alleen ingelogde gebruikers hun evenementen kunnen zien
requireLogin();

// Hier maken we een database-zoekopdracht (query) klaar
// Deze query zoekt alle evenementen die bij de ingelogde gebruiker horen
// 'ORDER BY date, time' zorgt ervoor dat de evenementen gesorteerd worden op datum en tijd
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date, time");

// Hier voeren we de query uit met de gebruikers-ID van de ingelogde gebruiker
// $_SESSION['user_id'] is de ID van de gebruiker die momenteel is ingelogd
$stmt->execute([$_SESSION['user_id']]);

// Hier halen we alle resultaten op en zetten ze in de variabele $events
// We gebruiken FETCH_ASSOC zodat we de resultaten kunnen gebruiken als een associatieve array
// Dit betekent dat we de gegevens kunnen opvragen via bijvoorbeeld $event['title']
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hier controleren we of er meldingen (flash-berichten) zijn om te tonen
// Dit zijn bijvoorbeeld berichten zoals "Evenement succesvol toegevoegd" of "Evenement verwijderd"
// Deze berichten worden opgeslagen in de sessie wanneer een actie is uitgevoerd
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Dit zijn meta-tags die extra informatie geven aan de browser -->
    <!-- charset="UTF-8" zorgt ervoor dat speciale tekens goed worden weergegeven -->
    <meta charset="UTF-8">
    
    <!-- Deze regel zorgt ervoor dat de website goed werkt op mobiele apparaten -->
    <!-- Het past de weergave aan de grootte van het scherm aan -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- De titel die in het browsertabblad wordt getoond -->
    <title>StudyMate - Dashboard</title>
    
    <!-- Hier laden we de CSS-bestanden (opmaak) voor de website -->
    <!-- style.css is ons eigen opmaakbestand met aangepaste stijlen -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Bootstrap is een verzameling kant-en-klare stijlen die we gebruiken voor een mooie opmaak -->
    <!-- We laden dit in vanaf een externe bron (CDN) zodat de website sneller laadt -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Dit is de navigatiebalk (menu) bovenaan de pagina -->
    <!-- navbar-dark bg-dark zorgt voor een donkere achtergrond met lichte tekst -->
    <!-- navbar-expand-lg zorgt ervoor dat het menu uitklapt op grotere schermen -->
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
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina - Tabel met evenementen -->
    <section class="container mt-5">
        <h2 class="text-center">Mijn Evenementen</h2>
        
        <!-- Toon flash-bericht als er een is (bijv. bevestiging na toevoegen/verwijderen) -->
        <?php if ($flash): ?>
            <p class="text-<?php echo $flash['type']; ?> text-center fw-bold"><?php echo $flash['message']; ?></p>
        <?php endif; ?>
        
        <!-- Tabel met alle evenementen van de gebruiker -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Categorie</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <!-- Rij voor elk evenement met alle details -->
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['date']); ?></td>
                        <td><?php echo htmlspecialchars($event['time']); ?></td>
                        <td><?php echo htmlspecialchars($event['category']); ?></td>
                        <td>
                            <!-- Actieknoppen voor elk evenement -->
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-warning">Bewerken</a>
                            <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Weet je het zeker?');">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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