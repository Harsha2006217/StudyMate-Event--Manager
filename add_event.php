<?php
/**
 * Evenement toevoegen - Pagina voor het aanmaken van nieuwe evenementen
 * 
 * Dit bestand maakt het mogelijk voor ingelogde gebruikers om nieuwe evenementen 
 * aan te maken in het StudyMate Event Manager systeem met verschillende categorieën,
 * datums en herinneringsopties.
 */

// Laad de benodigde functies en controleer of de gebruiker is ingelogd
require_once 'functions.php';
requireLogin(); // Nieuwe comment: Deze functie 'requireLogin()' controleert of de gebruiker is ingelogd.

// Definieer de beschikbare categorieën voor evenementen
$categories = ['school' => 'School', 'sociaal' => 'Sociaal', 'gaming' => 'Gaming']; // Nieuwe comment: '$categories' bevat alle categorieën die de gebruiker kan kiezen.

// Verwerk het formulier wanneer het wordt verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de formuliergegevens op en beveilig ze tegen XSS-aanvallen
    $title = sanitizeInput($_POST['title']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $category = $_POST['category'];
    
    // Controleer of de herinneringsoptie is aangevinkt
    $reminder = isset($_POST['reminder']) ? 1 : 0;
    $reminder_time = $reminder ? $_POST['reminder_time'] : null;

    // Valideer de ingevoerde gegevens
    if (empty($title)) {
        // Controleer of een titel is opgegeven
        $error = "Titel is verplicht.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        // Controleer of de datum niet in het verleden ligt
        $error = "Datum mag niet in het verleden liggen.";
    } elseif (!array_key_exists($category, $categories)) {
        // Controleer of de gekozen categorie geldig is
        $error = "Ongeldige categorie.";
    } else {
        // Alle validaties zijn succesvol, voeg het evenement toe aan de database
        $stmt = $pdo->prepare("INSERT INTO events (user_id, title, date, time, category, reminder, reminder_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $date, $time, $category, $reminder, $reminder_time]);
        
        // Toon een succesmelding en stuur de gebruiker terug naar het dashboard
        setFlashMessage('success', "Evenement '$title' succesvol toegevoegd!");
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement toevoegen</title>
    
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
                    <li class="nav-item"><a class="nav-link active" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina - Formulier voor het toevoegen van evenement -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement toevoegen</h2>
        
        <!-- Toon foutmelding als er een is -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Formulier voor het invoeren van evenementgegevens -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- Titel van het evenement -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
            </div>
            
            <!-- Datum van het evenement -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            
            <!-- Tijdstip van het evenement -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>
            
            <!-- Categorie selectie -->
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <?php foreach ($categories as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Herinneringsoptie checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder">
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>
            
            <!-- Herinneringstijd selectie -->
            <div class="mb-3">
                <!-- Dropdown menu voor het selecteren van de herinneringstijd -->
                <!-- Het label beschrijft het doel van het selectieveld voor gebruikers -->
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                <!-- Het select element met Bootstrap styling (form-select) -->
                <!-- Dit wordt automatisch in- of uitgeschakeld door JavaScript op basis van de checkbox -->
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <!-- Verschillende opties voor herinneringstijden -->
                    <!-- De value-attributen bevatten de waarden die naar de server worden verzonden -->
                    <option value="5 minuten ervoor">5 minuten ervoor</option>
                    <option value="30 minuten ervoor">30 minuten ervoor</option>
                    <option value="1 uur ervoor">1 uur ervoor</option>
                </select>
            </div>
            
            <!-- Formulierknoppen voor gebruikersacties -->
            <!-- De primaire actieknop (opslaan) met groene Bootstrap-styling -->
            <!-- w-100 zorgt ervoor dat de knop de volledige breedte inneemt -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>
            
            <!-- Een link die eruitziet als een knop om terug te gaan naar het overzichtspagina -->
            <!-- Deze link gebruikt de volgende elementen: -->
            <!-- - href="dashboard.php": stuurt de gebruiker naar het dashboard wanneer erop geklikt wordt -->
            <!-- - class="btn": maakt het een knop in plaats van een gewone link -->
            <!-- - btn-secondary: geeft de knop een grijze kleur om aan te geven dat dit een tweede keuze is -->
            <!-- - w-100: maakt de knop 100% breed, zodat deze de hele beschikbare ruimte gebruikt -->
            <!-- - mt-2: voegt een kleine ruimte (margin-top) toe boven de knop, zodat deze niet tegen de opslaan-knop aan zit -->
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>
    
    <!-- De voettekst (footer) die onderaan elke pagina staat -->
    <!-- Deze voettekst bevat: -->
    <!-- - bg-dark: een donkere achtergrondkleur (bijna zwart) -->
    <!-- - text-white: witte tekstkleur voor goede leesbaarheid op de donkere achtergrond -->
    <!-- - text-center: tekst wordt in het midden geplaatst -->
    <!-- - py-3: padding (ruimte) aan de bovenkant en onderkant, zodat de tekst niet tegen de randen zit -->
    <!-- - mt-5: een grote ruimte (margin-top) boven de footer, zodat er afstand is tussen de inhoud en de footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <!-- De tekst in de footer met het copyright-symbool en het jaartal -->
        <!-- Dit laat zien wie de eigenaar is van de website en wanneer het copyright is vastgelegd -->
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- Hier worden de JavaScript-bestanden ingeladen die de website interactief maken -->
    <!-- Dit eerste script laadt Bootstrap JavaScript in, dat zorgt voor: -->
    <!-- - De werking van het uitklapbare menu in de navigatiebalk -->
    <!-- - Interactieve elementen zoals dropdown-menu's -->
    <!-- - Responsive gedrag op verschillende schermgroottes -->
    <!-- We laden dit in vanaf een CDN (Content Delivery Network) voor snelle laadtijden -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dit is ons eigen JavaScript-bestand met specifieke functies voor deze website -->
    <!-- In dit bestand staan onder andere deze functies: -->
    <!-- - Het automatisch in- of uitschakelen van het herinneringstijd-veld wanneer de checkbox wordt aan/uitgezet -->
    <!-- - Animaties die elementen laten verschijnen met een mooi effect -->
    <!-- - Interactieve feedback wanneer gebruikers op knoppen klikken of formulieren invullen -->
    <script src="script.js"></script>
</body>
</html>