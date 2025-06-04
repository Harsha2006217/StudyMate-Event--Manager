<?php
/**
 * Wachtwoord Reset Script
 * 
 * Dit bestand verwerkt het resetten van wachtwoorden via een unieke token.
 * Gebruikers kunnen hier een nieuw wachtwoord instellen nadat ze een 
 * wachtwoordreset hebben aangevraagd via de "Wachtwoord vergeten" functie.
 */

// Laad de benodigde functies
require_once 'functions.php';

// Controleer of de gebruiker al is ingelogd; stuur door naar dashboard indien ja
// Een ingelogde gebruiker heeft geen wachtwoordreset nodig
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// Valideer de reset token uit de URL-parameter
// filter_input wordt gebruikt voor veilige verwerking van de parameter
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
if (!$token) {
    // Stop de uitvoering als er geen geldige token aanwezig is
    die("Ongeldig token.");
}

// Controleer of de token in de database bestaat en nog niet is verlopen
// De reset_expiry datum moet in de toekomst liggen (groter dan NOW())
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Als er geen gebruiker is gevonden met deze token (of de token is verlopen)
if (!$user) {
    // Stop de uitvoering en toon een foutmelding
    die("Token ongeldig of verlopen.");
}

// Verwerk het formulier als het is verzonden (POST-verzoek)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal het nieuwe wachtwoord op met nullish coalescing operator (??)
    // Dit geeft een lege string als de parameter niet bestaat
    $password = $_POST['password'] ?? '';
    
    // Valideer het wachtwoord op lengte (minimaal 8 tekens)
    if (strlen($password) < 8) {
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } else {
        // Hash het nieuwe wachtwoord voor veilige opslag in de database
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Update de gebruiker met het nieuwe wachtwoord en verwijder de reset token
        // Door reset_token en reset_expiry op NULL te zetten kan de token niet opnieuw worden gebruikt
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        
        // Toon een succesmelding op de inlogpagina
        setFlashMessage('success', 'Wachtwoord succesvol gewijzigd!');
        
        // Stuur de gebruiker naar de inlogpagina om in te loggen met het nieuwe wachtwoord
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Wachtwoord resetten</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    <section class="container mt-5">
        <h2 class="text-center">Wachtwoord resetten</h2>
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="password" class="form-label">Nieuw wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Wachtwoord wijzigen</button>
        </form>
    </section>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>