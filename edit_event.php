<?php
require_once 'functions.php';
requireLogin();

$id = $_GET['id'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$event = $stmt->fetch();

if (!$event) die("Evenement niet gevonden.");

$categories = ['school' => 'School', 'sociaal' => 'Sociaal', 'gaming' => 'Gaming'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $category = $_POST['category'];
    $reminder = isset($_POST['reminder']) ? 1 : 0;
    $reminder_time = $reminder ? $_POST['reminder_time'] : null;

    if (empty($title)) {
        $error = "Titel is verplicht.";
    } elseif (!array_key_exists($category, $categories)) {
        $error = "Ongeldige categorie.";
    } else {
        $stmt = $pdo->prepare("UPDATE events SET title = ?, date = ?, time = ?, category = ?, reminder = ?, reminder_time = ? WHERE id = ?");
        $stmt->execute([$title, $date, $time, $category, $reminder, $reminder_time, $id]);
        $success = "Evenement '$title' succesvol bijgewerkt!";
        header("Refresh: 2; url=dashboard.php");
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement bewerken</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement bewerken</h2>
        <?php 
        if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; 
        if (isset($success)) echo "<p class='text-success text-center fw-bold'>$success</p>"; 
        ?>
        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $event['date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo $event['time']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <?php 
                    foreach ($categories as $key => $value) {
                        $selected = ($event['category'] === $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder" <?php if ($event['reminder']) echo 'checked'; ?>>
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>
            <div class="mb-3">
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <option value="5 minuten ervoor" <?php if ($event['reminder_time'] === '5 minuten ervoor') echo 'selected'; ?>>5 minuten ervoor</option>
                    <option value="30 minuten ervoor" <?php if ($event['reminder_time'] === '30 minuten ervoor') echo 'selected'; ?>>30 minuten ervoor</option>
                    <option value="1 uur ervoor" <?php if ($event['reminder_time'] === '1 uur ervoor') echo 'selected'; ?>>1 uur ervoor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Opslaan</button>
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 StudyMate Event Manager</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>