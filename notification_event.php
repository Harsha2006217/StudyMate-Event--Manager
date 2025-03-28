<?php
require_once 'functions.php';
requireLogin();

// Simpele in-app notificatie (uitbreidbaar met e-mail/push)
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? AND reminder = 1 AND date >= CURDATE()");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll();

$notifications = [];
foreach ($events as $event) {
    $notifications[] = "Herinnering: {$event['title']} op {$event['date']} om {$event['time']} ({$event['reminder_time']})";
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>StudyMate - Notificaties</title>
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
    <section class="container mt-5">
        <h2 class="text-center">Notificaties</h2>
        <ul class="list-group">
            <?php foreach ($notifications as $note): ?>
                <li class="list-group-item"><?php echo $note; ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 StudyMate Event Manager</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>