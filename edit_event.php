<?php
/**
 * Dit script maakt het mogelijk om een evenement te bewerken.
 * Het bevat zowel de logica voor het verwerken van formulierdata als het tonen van het formulier.
 * Alleen de eigenaar van het evenement kan dit bewerken.
 */

// Laadt hulpfuncties en controleert of de gebruiker is ingelogd
require_once 'functions.php'; // Functies zoals sanitizeInput en setFlashMessage worden hier geladen
requireLogin(); // Controleert of de gebruiker is ingelogd, anders wordt hij doorgestuurd

// Haalt het evenement-ID uit de URL en valideert het
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Zorgt ervoor dat alleen een geldig ID wordt gebruikt
if ($id === false || $id === null) {
    die("Ongeldig evenement-ID."); // Stopt de uitvoering als het ID niet geldig is
}

// Haalt evenementgegevens op uit de database
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?"); // SQL-query om het evenement op te halen
$stmt->execute([$id, $_SESSION['user_id']]); // Voert de query uit met het ID en de ingelogde gebruiker
$event = $stmt->fetch(PDO::FETCH_ASSOC); // Haalt de gegevens op als associatieve array

// Controleert of het evenement bestaat en van de huidige gebruiker is
if (!$event) {
    die("Evenement niet gevonden."); // Stopt de uitvoering als het evenement niet bestaat
}

// Definieert de beschikbare categorieën
$categories = ['school' => 'School', 'sociaal' => 'Sociaal', 'gaming' => 'Gaming']; // Associatieve array met categorieën

// Verwerkt het formulier wanneer het wordt verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haalt formulierdata op en past beveiliging toe
    $title = sanitizeInput($_POST['title']); // Verwijdert gevaarlijke tekens uit de titel
    $date = $_POST['date']; // Haalt de datum op
    $time = $_POST['time']; // Haalt de tijd op
    $category = $_POST['category']; // Haalt de categorie op
    
    // Controleert of de herinnering is aangevinkt
    $reminder = isset($_POST['reminder']) ? 1 : 0; // Zet de herinnering op 1 als aangevinkt, anders 0
    $reminder_time = $reminder ? $_POST['reminder_time'] : null; // Herinneringstijd alleen instellen als herinnering actief is

    // Valideert de ingevoerde gegevens
    if (empty($title)) {
        $error = "Titel is verplicht."; // Geeft een foutmelding als de titel leeg is
    } elseif (!array_key_exists($category, $categories)) {
        $error = "Ongeldige categorie."; // Geeft een foutmelding als de categorie niet geldig is
    } else {
        // Update de gegevens in de database
        $stmt = $pdo->prepare("UPDATE events SET title = ?, date = ?, time = ?, category = ?, reminder = ?, reminder_time = ? WHERE id = ?");
        $stmt->execute([$title, $date, $time, $category, $reminder, $reminder_time, $id]); // Voert de update uit
        
        // Slaat een succesmelding op en stuurt de gebruiker terug naar het dashboard
        setFlashMessage('success', "Evenement '$title' succesvol bijgewerkt!");
        header("Location: dashboard.php"); // Redirect naar het dashboard
        exit(); // Stopt verdere uitvoering
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement bewerken</title>
    <link rel="stylesheet" href="style.css"> <!-- Link naar eigen stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
</head>
<body>
    <!-- Navigatiebalk -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a> <!-- Merknaam -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span> <!-- Hamburger menu -->
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Navigatie-items -->
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Formulier voor het bewerken van een evenement -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement bewerken</h2>
        
        <!-- Foutmelding -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Formulier -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- Titel -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            
            <!-- Datum -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $event['date']; ?>" required>
            </div>
            
            <!-- Tijd -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo $event['time']; ?>" required>
            </div>
            
            <!-- Categorie -->
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <?php foreach ($categories as $key => $value): ?>
                        <option value="<?php echo $key; ?>" <?php echo $event['category'] === $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Herinnering -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder" <?php echo $event['reminder'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>
            
            <!-- Herinneringstijd -->
            <div class="mb-3">
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <option value="5 minuten ervoor" <?php echo $event['reminder_time'] === '5 minuten ervoor' ? 'selected' : ''; ?>>5 minuten ervoor</option>
                    <option value="30 minuten ervoor" <?php echo $event['reminder_time'] === '30 minuten ervoor' ? 'selected' : ''; ?>>30 minuten ervoor</option>
                    <option value="1 uur ervoor" <?php echo $event['reminder_time'] === '1 uur ervoor' ? 'selected' : ''; ?>>1 uur ervoor</option>
                </select>
            </div>
            
            <!-- Knoppen -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>
    
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script> <!-- Eigen script -->
</body>
</html>
