<?php
/**
 * "Wachtwoord Vergeten" Pagina
 * Dit bestand biedt gebruikers de mogelijkheid om een wachtwoord reset aan te vragen.
 * Het genereert een unieke resetlink en toont deze aan de gebruiker.
 */

// Importeren van functies uit functions.php
require_once 'functions.php'; // Zorgt ervoor dat alle benodigde functies beschikbaar zijn.

// Controleert of de gebruiker al is ingelogd
if (isLoggedIn()) {
    // Als de gebruiker ingelogd is, wordt hij doorgestuurd naar het dashboard.
    header("Location: dashboard.php");
    exit(); // Voorkomt dat de rest van de code wordt uitgevoerd.
}

// Controleert of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haalt het ingevoerde e-mailadres op en maakt het veilig tegen XSS-aanvallen.
    $email = sanitizeInput($_POST['email']); // sanitizeInput() verwijdert schadelijke code.

    // Genereert een unieke token voor de wachtwoord reset.
    $token = generateResetToken(); // Deze token wordt later gebruikt in de resetlink.

    // Stelt een verloopdatum in voor de token (1 uur vanaf nu).
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Zorgt ervoor dat de link niet onbeperkt geldig is.

    // Update de database met de reset-token en de verloopdatum.
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]); // Voorkomt SQL-injectie door gebruik van prepared statements.

    // Geeft een succesbericht aan de gebruiker.
    setFlashMessage('success', "Controleer je e-mail voor de resetlink (simulatie: reset_password.php?token=$token).");

    // Redirect naar de inlogpagina na het verwerken van het formulier.
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-tags voor juiste karakterset en responsief ontwerp -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Wachtwoord vergeten</title>
    
    <!-- CSS-bestanden voor styling -->
    <link rel="stylesheet" href="style.css"> <!-- Algemene styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap voor responsief ontwerp -->
</head>
<body>
    <!-- Navigatiebalk -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a> <!-- Applicatienaam -->
        </div>
    </nav>
    
    <!-- Hoofdgedeelte met het formulier -->
    <section class="container mt-5">
        <h2 class="text-center">Wachtwoord vergeten</h2>
        
        <!-- Formulier voor het aanvragen van een resetlink -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- E-mailadres invoerveld -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required> <!-- 'required' zorgt ervoor dat het veld niet leeg kan worden verzonden -->
            </div>
            
            <!-- Verzendknop -->
            <button type="submit" class="btn btn-primary w-100">Resetlink aanvragen</button>
            
            <!-- Link terug naar de inlogpagina -->
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p>
        </form>
    </section>
    
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p> <!-- Copyright informatie -->
    </footer>
    
    <!-- JavaScript-bestanden -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap functionaliteit -->
    <script src="script.js"></script> <!-- Algemene scripts -->
</body>
</html>
