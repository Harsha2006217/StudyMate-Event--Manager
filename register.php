<?php
/**
 * Registratiepagina - Hier kan een nieuwe gebruiker een account aanmaken
 * 
 * Deze pagina doet het volgende:
 * 1. Controleren of iemand al is ingelogd (dan hoeft registreren niet meer)
 * 2. Het registratieformulier tonen met velden voor e-mail en wachtwoord
 * 3. De ingevulde gegevens controleren op juistheid
 * 4. Een nieuw account aanmaken als alles klopt
 * 5. De gebruiker doorsturen naar de inlogpagina om in te loggen met het nieuwe account
 */

// We laden eerst alle hulpfuncties in die we nodig hebben
require_once 'functions.php';

// We controleren of de gebruiker al is ingelogd
// Als dat zo is, heeft registreren geen zin meer
// Dan sturen we de gebruiker meteen door naar het dashboard (de hoofdpagina na inloggen)
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// Dit deel wordt alleen uitgevoerd als het formulier is ingevuld en verzonden
// We controleren of er op de registreerknop is geklikt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // We halen het ingevulde e-mailadres op en maken het veilig
    // sanitizeInput verwijdert gevaarlijke code die kwaadwillenden zouden kunnen invoeren
    // ?? '' zorgt ervoor dat als er niets is ingevuld, we een lege tekst gebruiken
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // We halen het ingevulde wachtwoord op
    // We beveiligen het wachtwoord niet met sanitizeInput omdat we het gaan versleutelen
    $password = $_POST['password'] ?? '';

    // We controleren of het e-mailadres wel echt een e-mailadres is
    // filter_var controleert of het e-mailadres een @ teken heeft en er goed uitziet
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Als het geen geldig e-mailadres is, maken we een foutmelding
        $error = "Ongeldig e-mailadres.";
    } 
    // We controleren of het wachtwoord lang genoeg is (minstens 8 tekens)
    // strlen telt het aantal tekens in het wachtwoord
    elseif (strlen($password) < 8) {
        // Als het wachtwoord te kort is, maken we een foutmelding
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } 
    // Als beide controles goed zijn, gaan we het account aanmaken
    else {
        // We gebruiken try-catch om eventuele fouten netjes af te handelen
        try {
            // We versleutelen het wachtwoord zodat het veilig kan worden opgeslagen
            // Versleutelde wachtwoorden kunnen niet worden teruggelezen, maar wel worden gecontroleerd
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // We maken een veilige database-opdracht om de nieuwe gebruiker op te slaan
            // De vraagtekens (?) zijn plaatshouders voor het e-mailadres en wachtwoord
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            
            // We voeren de opdracht uit en vullen het e-mailadres en versleutelde wachtwoord in
            $stmt->execute([$email, $hashed_password]);
            
            // We maken een succesmelding voor de gebruiker
            // Deze wordt getoond op de inlogpagina
            setFlashMessage('success', 'Registratie succesvol! Log in om te beginnen.');
            
            // We sturen de gebruiker door naar de inlogpagina om in te loggen
            header("Location: index.php");
            
            // We stoppen het script hier, omdat de gebruiker toch wordt doorgestuurd
            exit();
        } 
        // Als er iets mis gaat bij het opslaan, vangen we de fout op
        catch (PDOException $e) {
            // De meest waarschijnlijke fout is dat het e-mailadres al bestaat
            // We geven een duidelijke foutmelding aan de gebruiker
            $error = "E-mailadres bestaat al.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Deze regels geven informatie aan de browser over de pagina -->
    <!-- charset="UTF-8" zorgt ervoor dat speciale tekens goed worden weergegeven -->
    <meta charset="UTF-8">
    <!-- Deze regel zorgt dat de pagina er goed uitziet op mobiele telefoons -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- De titel die bovenin het browsertabblad wordt weergegeven -->
    <title>StudyMate - Registreren</title>
    <!-- CSS-bestanden voor opmaak en stijl -->
    <!-- Eigen stylesheet voor specifieke stijlen van de applicatie -->
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS voor kant-en-klare stijlen en responsiviteit -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk bovenaan de pagina -->
    <!-- Bevat de naam van de applicatie en is altijd zichtbaar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Naam van de applicatie, klikbaar logo -->
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdgedeelte van de pagina, hier komt het registratieformulier -->
    <section class="container mt-5">
        <!-- Titel van de sectie, groot en gecentreerd -->
        <h2 class="text-center">Registreren</h2>
        
        <!-- Foutmelding sectie -->
        <!-- Alleen zichtbaar als er een fout is, bijvoorbeeld bij een ongeldig e-mailadres -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Registratieformulier -->
        <!-- Het formulier is gecentreerd met Bootstrap klassen en wordt via POST verzonden -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- E-mailadres invoerveld -->
            <!-- Het veld behoudt de ingevoerde waarde bij validatiefouten, met XSS-bescherming via htmlspecialchars -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            
            <!-- Wachtwoord invoerveld -->
            <!-- Type password zorgt ervoor dat het wachtwoord verborgen wordt tijdens het typen -->
            <div class="mb-3">
                <label for="password" class="form-label">Wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <!-- Registratie-knop -->
            <!-- De knop is groen (success) en neemt de volledige breedte in (w-100) -->
            <button type="submit" class="btn btn-success w-100">Registreren</button>
            
            <!-- Link terug naar de inlogpagina -->
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p>
        </form>
    </section>
    
    <!-- Voettekst van de pagina -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript-bestanden voor interactiviteit -->
    <!-- Bootstrap JavaScript voor responsieve componenten -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Eigen JavaScript voor aanvullende functionaliteit -->
    <script src="script.js"></script>
</body>
</html>