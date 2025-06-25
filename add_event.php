<?php
/**
 * Pagina: Evenement toevoegen
 * Deze pagina stelt gebruikers in staat om nieuwe evenementen aan te maken in het StudyMate systeem.
 */

// Importeert basisfunctionaliteiten zoals database-verbinding en logincontrole
require_once 'functions.php';

// Controleert of de gebruiker ingelogd is, anders wordt hij teruggestuurd naar de loginpagina
requireLogin(); 

// Array met beschikbare categorieën voor evenementen
$categories = [
    'school' => 'School',    // Studiegerelateerde activiteiten
    'sociaal' => 'Sociaal',  // Sociale activiteiten
    'gaming' => 'Gaming'     // Game-evenementen
];

// Controleert of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Beveiligt invoer tegen XSS-aanvallen
    $title = sanitizeInput($_POST['title']);
    $date = $_POST['date'];         
    $time = $_POST['time'];         
    $category = $_POST['category']; 
    $reminder = isset($_POST['reminder']) ? 1 : 0; // Controleert of herinnering is aangevinkt
    $reminder_time = $reminder ? $_POST['reminder_time'] : null; // Herinneringstijd indien ingesteld

    // Validatie van gebruikersinvoer
    if (empty($title)) {
        $error = "Titel is verplicht."; // Titel mag niet leeg zijn
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $error = "Datum mag niet in het verleden liggen."; // Datum moet in de toekomst liggen
    } elseif (!array_key_exists($category, $categories)) {
        $error = "Ongeldige categorie."; // Controleert of categorie geldig is
    } else {
        // Bereidt SQL-query voor om gegevens veilig in te voegen
        $stmt = $pdo->prepare(
            "INSERT INTO events (user_id, title, date, time, category, reminder, reminder_time) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        // Voert de query uit met de verzamelde gegevens
        $stmt->execute([
            $_SESSION['user_id'], // Gebruikers-ID van de ingelogde gebruiker
            $title,               // Titel van het evenement
            $date,                // Datum van het evenement
            $time,                // Tijd van het evenement
            $category,            // Categorie van het evenement
            $reminder,            // Herinnering (1 = ja, 0 = nee)
            $reminder_time        // Herinneringstijd
        ]);

        // Stelt een succesmelding in en verwijst naar het dashboard
        setFlashMessage('success', "Evenement '$title' succesvol toegevoegd!");
        header("Location: dashboard.php");
        exit(); // Stopt verdere uitvoering
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor codering en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement toevoegen</title>
    <!-- Koppeling naar eigen stylesheet en Bootstrap -->
    <link rel="stylesheet" href="style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
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

    <!-- Formulier voor het toevoegen van een evenement -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement toevoegen</h2>

        <!-- Foutmelding indien validatie mislukt -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Formulier -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- Titel -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
            </div>

            <!-- Datum -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <!-- Tijd -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>

            <!-- Categorie -->
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <?php foreach ($categories as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Herinnering -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder">
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>

            <!-- Herinneringstijd -->
            <div class="mb-3">
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <option value="5 minuten ervoor">5 minuten ervoor</option>
                    <option value="30 minuten ervoor">30 minuten ervoor</option>
                    <option value="1 uur ervoor">1 uur ervoor</option>
                </select>
            </div>

            <!-- Opslaan -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>
            <!-- Terug naar overzicht -->
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>

    <!-- Voettekst -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>

    <!-- JavaScript-bestanden -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
