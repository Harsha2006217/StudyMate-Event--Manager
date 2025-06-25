<?php
/**
 * Dashboard - StudyMate Event Manager
 * Dit bestand toont het gebruikersdashboard na inloggen.
 * Hier kunnen gebruikers hun evenementen bekijken, beheren en verwijderen.
 */

// Importeert functies.php voor databaseverbinding en gebruikersauthenticatie
require_once 'functions.php';

// Controleert of de gebruiker is ingelogd
// Als de gebruiker niet is ingelogd, wordt deze doorverwezen naar de inlogpagina
requireLogin();

// Bereidt een SQL-query voor om alle evenementen van de ingelogde gebruiker op te halen
// De query gebruikt een prepared statement om SQL-injectie te voorkomen
$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date, time");

// Voert de query uit met de gebruikers-ID uit de sessie
$stmt->execute([$_SESSION['user_id']]);

// Haalt alle evenementen op en slaat ze op in een associatieve array
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Haalt eventuele flash-berichten op voor gebruikersfeedback
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-gegevens voor correcte weergave en responsiviteit -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Titel van de pagina -->
    <title>StudyMate - Dashboard</title>
    
    <!-- CSS-bestanden voor opmaak -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Applicatienaam als link naar de homepage -->
            <a class="navbar-brand" href="#">StudyMate</a>
            
            <!-- Hamburgermenu voor mobiele weergave -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigatie-items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Links naar verschillende pagina's -->
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina -->
    <section class="container mt-5">
        <!-- Titel van de pagina -->
        <h2 class="text-center">Mijn Evenementen</h2>
        
        <!-- Flash-berichten voor gebruikersfeedback -->
        <?php if ($flash): ?>
            <p class="text-<?php echo $flash['type']; ?> text-center fw-bold"><?php echo $flash['message']; ?></p>
        <?php endif; ?>
        
        <!-- Tabel met evenementen -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <!-- Kolomkoppen -->
                    <th>Titel</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Categorie</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loopt door alle evenementen en toont ze in de tabel -->
                <?php foreach ($events as $event): ?>
                    <tr>
                        <!-- Toont de details van elk evenement -->
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['date']); ?></td>
                        <td><?php echo htmlspecialchars($event['time']); ?></td>
                        <td><?php echo htmlspecialchars($event['category']); ?></td>
                        <td>
                            <!-- Actieknoppen voor bewerken en verwijderen -->
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-warning">Bewerken</a>
                            <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Weet je het zeker?');">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
