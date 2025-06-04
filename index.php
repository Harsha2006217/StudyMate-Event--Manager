<?php
/**
 * Inlogpagina voor StudyMate Event Manager
 * 
 * Dit is de startpagina van de applicatie waar gebruikers kunnen inloggen.
 * De pagina bevat een inlogformulier en links naar registratie en wachtwoordherstel.
 * Als de gebruiker al is ingelogd, wordt hij doorgestuurd naar het dashboard.
 */

// Laad de benodigde functies
require_once 'functions.php';

// Controleer of de gebruiker al is ingelogd; stuur door naar dashboard indien ja
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// Verwerk het inlogformulier als het is verzonden (POST-verzoek)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de ingevoerde gegevens op en beveilig ze
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Wachtwoord niet sanitizen omdat het gehashed wordt

    // Zoek de gebruiker op in de database op basis van e-mailadres
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Controleer of de gebruiker bestaat en het wachtwoord klopt
    if ($user && password_verify($password, $user['password'])) {
        // Sla gebruikers-ID op in sessie om de login te onthouden
        $_SESSION['user_id'] = $user['id'];
        // Stuur de gebruiker naar het dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Toon foutmelding als inloggegevens onjuist zijn
        $error = "Ongeldige e-mail of wachtwoord.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Inloggen</title>
    <!-- CSS-bestanden voor styling -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk bovenaan de pagina -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina - Inlogformulier -->
    <section class="container mt-5">
        <h2 class="text-center">Inloggen</h2>
        
        <!-- Toon foutmelding als er een is (bij ongeldige inloggegevens) -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Inlogformulier -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- E-mailadres invoerveld -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <!-- Wachtwoord invoerveld -->
            <div class="mb-3">
                <label for="password" class="form-label">Wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <!-- Inlogknop -->
            <button type="submit" class="btn btn-primary w-100">Inloggen</button>
            
            <!-- Links naar registratie en wachtwoord vergeten -->
            <p class="mt-2 text-center">
                <a href="register.php">Account aanmaken</a> | <a href="forgot_password.php">Wachtwoord vergeten?</a>
            </p>
        </form>
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