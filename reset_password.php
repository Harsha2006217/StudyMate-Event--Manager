<?php
/**
 * Wachtwoord reset functionaliteit
 * 
 * Dit script verwerkt aanvragen voor het opnieuw instellen van wachtwoorden.
 * Gebruikers komen hier terecht via een speciale link in een e-mail na een 'wachtwoord vergeten' verzoek.
 * 
 * Belangrijke beveiligingsfuncties:
 * - Controleert of een unieke token geldig is en niet verlopen
 * - Valideert het nieuwe wachtwoord op veiligheid
 * - Slaat wachtwoorden veilig op met hashing
 * - Zorgt dat tokens maar één keer gebruikt kunnen worden
 */

// Importeren van gemeenschappelijke functies die nodig zijn voor authenticatie
require_once 'functions.php';

// Controleer of gebruiker al ingelogd is; ingelogde gebruikers hebben geen wachtwoordreset nodig
if (isLoggedIn()) {
    // Stuur gebruiker direct door naar het dashboard om onnodige toegang tot reset pagina te voorkomen
    header("Location: dashboard.php");
    exit();
}

// Haal de reset token uit de URL parameters en maak deze veilig door speciale tekens te filteren
// Dit voorkomt XSS (Cross-Site Scripting) aanvallen
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

// Controleer of de token bestaat - zonder geldige token mag de gebruiker niet verder
if (!$token) {
    // Stop de uitvoering direct en toon een foutmelding aan de gebruiker
    die("Ongeldig token.");
}

// Zoek in de database naar een gebruiker met deze token die nog geldig is (niet verlopen)
// De datum/tijd vergelijking zorgt ervoor dat verlopen tokens niet meer werken
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Als er geen gebruiker met een geldige token is gevonden, stop het proces
if (!$user) {
    // Beveiligingsmaatregel: geen specifieke reden geven waarom het mislukt is
    die("Token ongeldig of verlopen.");
}

// Controleer of het formulier is verzonden met POST methode
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal het nieuwe wachtwoord veilig op uit het formulier
    // De ?? operator zorgt dat we een lege string krijgen als het veld niet bestaat
    $password = $_POST['password'] ?? '';
    
    // Valideer het wachtwoord op minimale lengte voor basale veiligheid
    if (strlen($password) < 8) {
        // Sla de foutmelding op om later in het formulier te tonen
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } else {
        // Hash het wachtwoord voor veilige opslag - nooit plain text wachtwoorden opslaan!
        // BCRYPT is een veilige hashing methode die automatisch een salt toevoegt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Update de gebruiker met het nieuwe wachtwoord en maak de reset token ongeldig
        // Door token en expiry op NULL te zetten voorkom je hergebruik van de token
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        
        // Sla een succesbericht op in de sessie voor weergave op de inlogpagina
        setFlashMessage('success', 'Wachtwoord succesvol gewijzigd!');
        
        // Stuur gebruiker door naar de inlogpagina om in te loggen met het nieuwe wachtwoord
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Basis meta-informatie voor responsiveness en character encoding -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Wachtwoord resetten</title>
    <!-- Externe stylesheet en Bootstrap voor consistente styling -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk bovenaan de pagina voor consistente gebruikservaring -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdsectie met het wachtwoord reset formulier -->
    <section class="container mt-5">
        <h2 class="text-center">Wachtwoord resetten</h2>
        
        <!-- Toon foutmeldingen als die er zijn na validatie -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Wachtwoord reset formulier - verstuurt gegevens naar dezelfde pagina (POST) -->
        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="password" class="form-label">Nieuw wachtwoord</label>
                <!-- Input veld voor het nieuwe wachtwoord, met required attribuut voor basale validatie -->
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <!-- Verzendknop voor het formulier met Bootstrap styling -->
            <button type="submit" class="btn btn-success w-100">Wachtwoord wijzigen</button>
        </form>
    </section>
    
    <!-- Footer met copyright informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript libraries voor interactieve elementen -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>