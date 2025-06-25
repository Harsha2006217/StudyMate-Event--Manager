<?php
/**
 * Evenement Bewerken Script
 * 
 * Dit script maakt het mogelijk voor gebruikers om hun eigen evenementen aan te passen.
 * Het bevat zowel de verwerkingslogica als het formulier om de gegevens te wijzigen.
 * Beveiliging is ingebouwd zodat gebruikers alleen hun eigen evenementen kunnen bewerken.
 */

// Laadt hulpfuncties en databaseverbinding, controleert of de gebruiker is ingelogd
require_once 'functions.php';
requireLogin();

// Haalt het evenement-ID uit de URL en controleert of het een geldig geheel getal is
// Dit voorkomt SQL-injectie door ongeldige invoer te filteren
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    die("Ongeldig evenement-ID.");
}

// Haalt evenementgegevens op uit de database, maar alleen als het van de ingelogde gebruiker is
// Dit is een beveiligingsmaatregel die voorkomt dat gebruikers andermans evenementen kunnen wijzigen
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Controleert of het evenement bestaat en van de huidige gebruiker is
if (!$event) {
    die("Evenement niet gevonden.");
}

// Definieert de beschikbare categorieën voor evenementen in een associatieve array
// De sleutel wordt in de database opgeslagen, de waarde wordt getoond aan de gebruiker
$categories = ['school' => 'School', 'sociaal' => 'Sociaal', 'gaming' => 'Gaming'];

// Verwerkt het formulier wanneer het wordt verzonden (POST-methode)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haalt alle formuliergegevens op en past beveiliging toe waar nodig
    // sanitizeInput verwijdert potentieel gevaarlijke tekens voor veilige verwerking
    $title = sanitizeInput($_POST['title']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $category = $_POST['category'];
    
    // Bepaalt of de herinneringsoptie is aangevinkt (1 = ja, 0 = nee)
    // Als herinnering niet is aangevinkt, wordt de herinneringstijd op null gezet
    $reminder = isset($_POST['reminder']) ? 1 : 0;
    $reminder_time = $reminder ? $_POST['reminder_time'] : null;

    // Valideert de ingevoerde gegevens voordat ze worden opgeslagen
    if (empty($title)) {
        $error = "Titel is verplicht.";
    } elseif (!array_key_exists($category, $categories)) {
        $error = "Ongeldige categorie.";
    } else {
        // Als alle gegevens geldig zijn, wordt het evenement bijgewerkt in de database
        // De voorbereidde statement (prepare) beschermt tegen SQL-injectie
        $stmt = $pdo->prepare("UPDATE events SET title = ?, date = ?, time = ?, category = ?, reminder = ?, reminder_time = ? WHERE id = ?");
        $stmt->execute([$title, $date, $time, $category, $reminder, $reminder_time, $id]);
        
        // Slaat een succesmelding op en stuurt de gebruiker terug naar het dashboard
        setFlashMessage('success', "Evenement '$title' succesvol bijgewerkt!");
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
    <title>StudyMate - Evenement bewerken</title>
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Navigatie-items -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina - Formulier voor het bewerken van een evenement -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement bewerken</h2>
        
        <!-- Toon foutmelding als er een is -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Formulier voor het bewerken van evenementgegevens, vooringevuld met bestaande waarden -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- Titel van het evenement -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            
            <!-- Datum van het evenement -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $event['date']; ?>" required>
            </div>
            
            <!-- Tijdstip van het evenement -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo $event['time']; ?>" required>
            </div>
            
            <!-- Categorie selectie -->
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <?php foreach ($categories as $key => $value): ?>
                        <!-- Markeer de huidige categorie als geselecteerd -->
                        <option value="<?php echo $key; ?>" <?php echo $event['category'] === $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Herinneringsoptie checkbox, aangevinkt indien reeds ingesteld -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder" <?php echo $event['reminder'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>
            
            <!-- Dit is de selectielijst voor het instellen van de herinneringstijd -->
            <!-- Deze wordt alleen gebruikt als de gebruiker de herinnering heeft aangezet met de checkbox -->
            <div class="mb-3">
                <!-- Dit label beschrijft wat de gebruiker gaat selecteren -->
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                
                <!-- Dit is de uitklaplijst (dropdown) waar gebruikers uit verschillende tijden kunnen kiezen -->
                <!-- Het id="reminder_time" zorgt ervoor dat dit veld gekoppeld is aan het label hierboven -->
                <!-- name="reminder_time" is de naam waarmee deze keuze wordt opgeslagen in de database -->
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <!-- Hieronder staan de drie keuzemogelijkheden voor de herinnering -->
                    <!-- Bij elke optie controleren we (met PHP) of dit de huidige instelling is -->
                    <!-- Als het de huidige instelling is, dan wordt 'selected' toegevoegd, zodat deze optie voorgeselecteerd is -->
                    <!-- Het vraagteken ? betekent: "als dit waar is, gebruik dan 'selected', anders niets" -->
                    
                    <!-- Optie 1: 5 minuten voor het evenement -->
                    <option value="5 minuten ervoor" <?php echo $event['reminder_time'] === '5 minuten ervoor' ? 'selected' : ''; ?>>5 minuten ervoor</option>
                    
                    <!-- Optie 2: 30 minuten voor het evenement -->
                    <option value="30 minuten ervoor" <?php echo $event['reminder_time'] === '30 minuten ervoor' ? 'selected' : ''; ?>>30 minuten ervoor</option>
                    
                    <!-- Optie 3: 1 uur voor het evenement -->
                    <option value="1 uur ervoor" <?php echo $event['reminder_time'] === '1 uur ervoor' ? 'selected' : ''; ?>>1 uur ervoor</option>
                </select>
            </div>
            
            <!-- Hier beginnen de knoppen waarmee de gebruiker acties kan uitvoeren -->
            
            <!-- Dit is de hoofdknop om de wijzigingen op te slaan -->
            <!-- type="submit" betekent dat deze knop het formulier zal versturen -->
            <!-- De groene kleur (btn-success) geeft aan dat dit een positieve actie is -->
            <!-- w-100 maakt de knop 100% breed, zodat deze het hele formulier vult -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>
            
            <!-- Dit is de tweede knop om terug te gaan zonder op te slaan -->
            <!-- Dit is eigenlijk een link (a) die eruitziet als een knop -->
            <!-- href="dashboard.php" betekent dat deze link naar het dashboard (overzichtspagina) gaat -->
            <!-- De grijze kleur (btn-secondary) laat zien dat dit niet de hoofdactie is -->
            <!-- mt-2 zorgt voor een klein beetje ruimte tussen deze knop en de knop erboven -->
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>
    
    <!-- Hier begint de voettekst (footer) die onderaan elke pagina staat -->
    <!-- Deze heeft een donkere achtergrond (bg-dark) en witte tekst (text-white) -->
    <!-- text-center zorgt dat de tekst in het midden staat -->
    <!-- py-3 zorgt voor ruimte boven en onder de tekst -->
    <!-- mt-5 zorgt voor veel ruimte tussen de inhoud en de footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <!-- Het copyright-symbool en jaartal -->
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- Hier worden externe bestanden ingeladen die de website interactiever maken -->
    
    <!-- Dit laadt Bootstrap in, een pakket met vooraf gemaakte functies -->
    <!-- Hierdoor werken bijvoorbeeld uitklapmenu's en animaties -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dit laadt ons eigen script in met speciale functies voor deze website -->
    <!-- Hierin staat bijvoorbeeld de code die ervoor zorgt dat het herinneringstijdveld -->
    <!-- automatisch wordt in- of uitgeschakeld wanneer je de checkbox aanklikt -->
    <script src="script.js"></script>
</body>
</html>