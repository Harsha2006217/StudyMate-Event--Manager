<?php
/**
 * Wachtwoord reset functionaliteit
 * Dit script verwerkt aanvragen voor het opnieuw instellen van wachtwoorden.
 * Gebruikers komen hier terecht via een speciale link in een e-mail na een 'wachtwoord vergeten' verzoek.
 */

// Gemeenschappelijke functies worden geïmporteerd voor hergebruik
require_once 'functions.php';

// Controleer of de gebruiker al ingelogd is
if (isLoggedIn()) {
    // Als de gebruiker ingelogd is, wordt hij doorgestuurd naar het dashboard
    header("Location: dashboard.php");
    exit(); // Voorkomt verdere uitvoering van de code
}

// Haal de reset token uit de URL en maak deze veilig
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING); // Voorkomt XSS-aanvallen

// Controleer of de token bestaat
if (!$token) {
    // Als er geen token is, toon een foutmelding en stop de uitvoering
    die("Ongeldig token.");
}

// Zoek in de database naar een gebruiker met deze token die nog geldig is
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]); // Voer de query uit met de token als parameter
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Haal de gebruiker op als array

// Controleer of er een geldige gebruiker is gevonden
if (!$user) {
    // Als er geen gebruiker is, toon een generieke foutmelding
    die("Token ongeldig of verlopen.");
}

// Controleer of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal het nieuwe wachtwoord op uit het formulier
    $password = $_POST['password'] ?? ''; // Zorgt ervoor dat er geen fout optreedt als het veld leeg is

    // Valideer het wachtwoord op minimale lengte
    if (strlen($password) < 8) {
        // Toon een foutmelding als het wachtwoord te kort is
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } else {
        // Hash het wachtwoord voor veilige opslag
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // BCRYPT voegt automatisch een salt toe

        // Update het wachtwoord in de database en maak de reset token ongeldig
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]); // Voer de update uit

        // Sla een succesbericht op in de sessie
        setFlashMessage('success', 'Wachtwoord succesvol gewijzigd!');

        // Stuur de gebruiker door naar de inlogpagina
        header("Location: index.php");
        exit(); // Voorkomt verdere uitvoering van de code
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Basis meta-informatie -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Wachtwoord resetten</title>
    <!-- Externe stylesheet en Bootstrap -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdsectie met het formulier -->
    <section class="container mt-5">
        <h2 class="text-center">Wachtwoord resetten</h2>
        
        <!-- Toon foutmeldingen -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Formulier voor wachtwoord reset -->
        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="password" class="form-label">Nieuw wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Wachtwoord wijzigen</button>
        </form>
    </section>
    
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
