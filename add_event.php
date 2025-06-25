<?php
/**
 * Pagina: Evenement toevoegen
 * 
 * Deze pagina stelt gebruikers in staat om nieuwe evenementen aan te maken in het StudyMate systeem.
 * De gebruiker kan alle details van een evenement invullen zoals titel, datum, tijd en categorie.
 * Ook kan er een herinnering worden ingesteld.
 */

// Importeert functions.php met basisfunctionaliteiten zoals database-verbinding en logincontrole
require_once 'functions.php';

// Controleert of gebruiker ingelogd is, anders wordt gebruiker teruggestuurd naar de loginpagina
// Dit is een beveiligingsmaatregel zodat alleen geauthenticeerde gebruikers evenementen kunnen toevoegen
requireLogin(); 

// Array met beschikbare categorieën voor evenementen
// De key wordt opgeslagen in de database, de value wordt getoond aan de gebruiker
$categories = [
    'school' => 'School',    // Voor studiegerelateerde activiteiten zoals lessen, deadlines, tentamens
    'sociaal' => 'Sociaal',  // Voor sociale activiteiten zoals feesten, ontmoetingen
    'gaming' => 'Gaming'     // Voor game-evenementen zoals toernooien, releases
];

// Formulierverwerking: deze code wordt alleen uitgevoerd wanneer het formulier is verzonden (POST methode)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Beveiligt invoer tegen XSS-aanvallen door invoer te filteren
    $title = sanitizeInput($_POST['title']);
    
    // Neemt de resterende formuliergegevens over
    $date = $_POST['date'];         
    $time = $_POST['time'];         
    $category = $_POST['category']; 

    // Bepaalt of een herinnering is ingesteld via de checkbox (waarde 1 als aangevinkt, anders 0)
    $reminder = isset($_POST['reminder']) ? 1 : 0;
    
    // Als herinnering is ingesteld, bewaar de gekozen herinneringstijd, anders null
    $reminder_time = $reminder ? $_POST['reminder_time'] : null;

    // Validatie van gebruikersinvoer om onjuiste gegevens te voorkomen
    if (empty($title)) {
        // Controleert of de titel is ingevuld (verplicht veld)
        $error = "Titel is verplicht.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        // Controleert of de datum niet in het verleden ligt (logische validatie)
        $error = "Datum mag niet in het verleden liggen.";
    } elseif (!array_key_exists($category, $categories)) {
        // Controleert of een geldige categorie is gekozen uit de vooraf gedefinieerde opties
        $error = "Ongeldige categorie.";
    } else {
        // Als alle validaties succesvol zijn, bereid een SQL-query voor om data veilig in te voegen
        // Prepared statements worden gebruikt om SQL-injectie te voorkomen
        $stmt = $pdo->prepare(
            "INSERT INTO events (user_id, title, date, time, category, reminder, reminder_time) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        // Voert de query uit met de verzamelde gegevens als parameters
        $stmt->execute([
            $_SESSION['user_id'], // Koppelt het evenement aan de ingelogde gebruiker
            $title,               // Titel van het evenement
            $date,                // Datum waarop het evenement plaatsvindt
            $time,                // Tijdstip waarop het evenement begint
            $category,            // Categorie van het evenement
            $reminder,            // Of een herinnering is ingesteld (1 = ja, 0 = nee)
            $reminder_time        // Tijd vooraf wanneer de herinnering moet worden getoond
        ]);

        // Stelt een succesmelding in die getoond wordt na doorverwijzing (flash message)
        setFlashMessage('success', "Evenement '$title' succesvol toegevoegd!");

        // Stuurt gebruiker terug naar dashboard na succesvol toevoegen
        header("Location: dashboard.php");
        exit(); // Stopt verdere uitvoering om zeker te zijn dat de redirect gebeurt
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor juiste codering en responsiveness op verschillende apparaten -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement toevoegen</title>
    <!-- Koppeling naar eigen stylesheet en Bootstrap voor layout en styling -->
    <link rel="stylesheet" href="style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk bovenaan de pagina met links naar belangrijke onderdelen -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo/merknaam met link naar homepage -->
            <a class="navbar-brand" href="#">StudyMate</a>
            <!-- Hamburger-menu voor mobiele weergave -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navigatiemenu met links naar verschillende pagina's -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- 'active' class markeert de huidige pagina in het menu -->
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hoofdgedeelte met het formulier voor het toevoegen van een evenement -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement toevoegen</h2>

        <!-- Conditionele weergave van foutmeldingen bij validatieproblemen -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Formulier voor het invoeren van evenementgegevens met POST-methode -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- Invoerveld voor de titel van het evenement -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <!-- Value-attribuut behoudt ingevulde waarde bij formulierfouten -->
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
            </div>

            <!-- Datumkiezer voor de evenementdatum -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <!-- Tijdkiezer voor de aanvangstijd van het evenement -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>

            <!-- Dropdown-menu voor het selecteren van een evenementcategorie -->
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <!-- Dynamisch gegenereerde opties op basis van de $categories array -->
                    <?php foreach ($categories as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Checkbox om herinneringen voor het evenement in/uit te schakelen -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder">
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>

            <!-- Dropdown voor het kiezen van een herinneringstijd, zichtbaar wanneer herinnering is ingeschakeld -->
            <div class="mb-3">
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <!-- Vooraf gedefinieerde opties voor herinneringstijden -->
                    <option value="5 minuten ervoor">5 minuten ervoor</option>
                    <option value="30 minuten ervoor">30 minuten ervoor</option>
                    <option value="1 uur ervoor">1 uur ervoor</option>
                </select>
            </div>

            <!-- Verzendknop voor het formulier -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>

            <!-- Link terug naar dashboard zonder wijzigingen op te slaan -->
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>

    <!-- Voettekst met copyright-informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>

    <!-- JavaScript-bestanden voor interactieve elementen en Bootstrap-functionaliteit -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>