<?php
/**
 * Wachtwoord opnieuw instellen - Voor als je je wachtwoord bent vergeten
 * 
 * Deze pagina heeft een belangrijk doel:
 * 1. Het laat gebruikers een nieuw wachtwoord kiezen als ze hun oude zijn vergeten
 * 2. Het controleert of ze wel echt een reset hebben aangevraagd (via een speciale code)
 * 3. Het zorgt ervoor dat het nieuwe wachtwoord veilig wordt opgeslagen
 * 
 * De gebruiker komt op deze pagina nadat hij/zij via de "Wachtwoord vergeten" functie
 * een e-mail heeft ontvangen met een speciale link naar deze pagina.
 */

// We laden eerst alle hulpfuncties in die we nodig hebben
require_once 'functions.php';

// We controleren of de gebruiker al is ingelogd
// Als iemand al is ingelogd, heeft hij/zij geen wachtwoordreset nodig
// Dan sturen we de gebruiker meteen door naar de hoofdpagina
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// We halen de speciale code (token) uit de link waarmee de gebruiker hier kwam
// De token staat in de URL, bijvoorbeeld: reset_password.php?token=abc123
// filter_input zorgt ervoor dat we de token veilig ophalen, zonder gevaarlijke tekens
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

// Als er geen token in de URL staat of als deze leeg is, stoppen we meteen
// De gebruiker moet een geldige link gebruiken die via e-mail is verstuurd
if (!$token) {
    // We stoppen het script en tonen een duidelijke foutmelding
    die("Ongeldig token.");
}

// Nu gaan we controleren of deze token echt bestaat in onze database
// We zoeken naar een gebruiker met deze token, waarbij de token nog niet is verlopen
// "reset_expiry > NOW()" betekent dat de vervaldatum in de toekomst moet liggen
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]);
// We halen de gevonden gebruiker op (als die bestaat)
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Als we geen gebruiker vinden met deze token, of als de token is verlopen
// (de vervaldatum ligt in het verleden), dan stoppen we hier
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