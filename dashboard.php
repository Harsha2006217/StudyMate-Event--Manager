<?php
/**
 * Dashboard (overzichtspagina) - StudyMate Event Manager
 * 
 * Dit bestand toont het gebruikersdashboard na inloggen met een persoonlijk overzicht
 * van alle evenementen die de gebruiker heeft aangemaakt. Hier kunnen gebruikers hun
 * evenementen beheren, bewerken en verwijderen.
 */

// Importeert functies.php met alle benodigde hulpfuncties voor de applicatie
// Dit bestand bevat essentiële functies voor databaseverbinding en gebruikersauthenticatie
require_once 'functions.php';

// Controleert of de gebruiker is ingelogd voordat toegang wordt verleend tot deze pagina
// Als de gebruiker niet is ingelogd, wordt deze automatisch doorverwezen naar de inlogpagina
requireLogin();

// Database query om alle evenementen van de ingelogde gebruiker op te halen
// Door gebruik te maken van een prepared statement wordt SQL-injectie voorkomen
// Evenementen worden gesorteerd op datum en tijd om aankomende events bovenaan te tonen
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date, time");

// Voert de query uit met de gebruikers-ID als parameter
// De gebruikers-ID komt uit de sessie die is aangemaakt tijdens het inlogproces
$stmt->execute([$_SESSION['user_id']]);

// Slaat alle gevonden evenementen op in de variabele $events als een associatieve array
// Hierdoor kunnen we later gemakkelijk de details van elk evenement tonen in de HTML-tabel
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Haalt flash-berichten op voor gebruikersfeedback (bijv. "Evenement succesvol toegevoegd")
// Deze berichten worden eenmalig getoond en daarna automatisch verwijderd uit de sessie
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-gegevens voor juiste karakterweergave en responsieve weergave -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Titel van de webpagina die in het tabblad wordt getoond -->
    <title>StudyMate - Dashboard</title>
    
    <!-- Verwijzingen naar CSS-bestanden voor de opmaak van de pagina -->
    <!-- Eerst onze eigen stylesheet, daarna Bootstrap voor responsieve layout -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk bovenaan de pagina voor sitenavigatie -->
    <!-- Deze navigatie is consistent aanwezig op alle pagina's van de applicatie -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo/naam van de applicatie die ook als link naar de homepage fungeert -->
            <a class="navbar-brand" href="#">StudyMate</a>
            
            <!-- Hamburgermenu-knop voor mobiele weergave wanneer scherm te klein wordt -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigatie-items die rechts in de navigatiebalk worden weergegeven -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Links naar verschillende pagina's binnen de applicatie -->
                    <!-- 'active' klasse markeert de huidige pagina in het menu -->
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdgedeelte van de pagina waarin de evenementen worden getoond -->
    <section class="container mt-5">
        <!-- Titel van de pagina die boven de evenemententabel wordt weergegeven -->
        <h2 class="text-center">Mijn Evenementen</h2>
        
        <!-- Toon flash-berichten als die er zijn (bijv. na toevoegen of verwijderen) -->
        <!-- Deze berichten geven de gebruiker directe feedback over hun acties -->
        <?php if ($flash): ?>
            <p class="text-<?php echo $flash['type']; ?> text-center fw-bold"><?php echo $flash['message']; ?></p>
        <?php endif; ?>
        
        <!-- Tabel met alle evenementen van de ingelogde gebruiker -->
        <!-- table-striped maakt afwisselend gekleurde rijen voor betere leesbaarheid -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <!-- Koppen van de tabelkolommen die aangeven welke informatie wordt getoond -->
                    <th>Titel</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Categorie</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop door elk evenement heen om alle evenementen van de gebruiker te tonen -->
                <?php foreach ($events as $event): ?>
                    <tr>
                        <!-- Toont de details van elk evenement in aparte kolommen -->
                        <!-- htmlspecialchars voorkomt XSS-aanvallen door speciale tekens veilig te coderen -->
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['date']); ?></td>
                        <td><?php echo htmlspecialchars($event['time']); ?></td>
                        <td><?php echo htmlspecialchars($event['category']); ?></td>
                        <td>
                            <!-- Actieknoppen voor elk evenement met de ID als parameter in de URL -->
                            <!-- Bewerken-knop leidt naar het formulier om het evenement aan te passen -->
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-warning">Bewerken</a>
                            
                            <!-- Verwijderen-knop met bevestigingsdialoog om onbedoeld verwijderen te voorkomen -->
                            <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Weet je het zeker?');">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    
    <!-- Voettekst onderaan de pagina met copyright-informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript-bestanden voor interactieve elementen zoals het mobiele menu -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>