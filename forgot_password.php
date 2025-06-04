<?php
/**
 * Wachtwoord Vergeten Pagina
 * 
 * Deze pagina stelt gebruikers in staat om een wachtwoordreset aan te vragen
 * wanneer ze hun wachtwoord zijn vergeten. Het genereert een unieke token
 * en zou normaal gesproken een e-mail sturen (in deze demo wordt dat gesimuleerd).
 */

// Laad de benodigde functies
require_once 'functions.php';

// Controleer of de gebruiker al is ingelogd; stuur door naar dashboard indien ja
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// Verwerk het formulier als het is verzonden (POST-verzoek)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal het e-mailadres op en beveilig het tegen XSS-aanvallen
    $email = sanitizeInput($_POST['email']);
    
    // Genereer een unieke token voor de wachtwoordreset
    $token = generateResetToken();
    
    // Stel de vervaldatum in (1 uur vanaf nu)
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Sla de token en vervaldatum op in de database voor de gebruiker met dit e-mailadres
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]);

    // Toon een bevestiging aan de gebruiker (in een echte applicatie zou hier een e-mail worden verstuurd)
    setFlashMessage('success', "Controleer je e-mail voor de resetlink (simulatie: reset_password.php?token=$token).");
    
    // Stuur de gebruiker terug naar de inlogpagina
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor de browser en responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Wachtwoord vergeten</title>
    
    <!-- CSS-bestanden voor styling -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Eenvoudige navigatiebalk bovenaan de pagina -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdinhoud van de pagina - Formulier voor wachtwoord vergeten -->
    <section class="container mt-5">
        <h2 class="text-center">Wachtwoord vergeten</h2>
        
        <!-- Formulier voor het aanvragen van een wachtwoordreset -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- E-mailadres invoerveld -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <!-- Knop om de resetlink aan te vragen -->
            <button type="submit" class="btn btn-primary w-100">Resetlink aanvragen</button>
            
            <!-- Link terug naar de inlogpagina -->
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p>
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