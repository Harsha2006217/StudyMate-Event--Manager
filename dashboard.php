<?php
/**
 * Dashboard - Hoofdpagina voor gebruikers van StudyMate Event Manager
 * 
 * Deze pagina toont een overzicht van alle evenementen die door de ingelogde gebruiker
 * zijn aangemaakt en biedt opties om deze evenementen te beheren (bewerken, verwijderen).
 */

// Laad de benodigde functies en controleer of de gebruiker is ingelogd
require_once 'functions.php';
requireLogin();

// Haal alle evenementen van de ingelogde gebruiker op uit de database
// Gesorteerd op datum en tijd (chronologisch)
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date, time");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Haal eventuele flash-berichten op (bijv. bevestigingen van toevoegen/wijzigen)
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Dashboard</title>
    
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