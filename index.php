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
    // Als de gebruiker al is ingelogd, sturen we hem direct door naar het dashboard
    // Dit voorkomt dat ingelogde gebruikers opnieuw moeten inloggen
    header("Location: dashboard.php");
    exit();
}

// Dit blok verwerkt het inlogformulier wanneer de gebruiker op de inlogknop klikt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // We halen hier het e-mailadres veilig op uit het formulier
    // sanitizeInput() verwijdert gevaarlijke karakters om hackers tegen te houden
    $email = sanitizeInput($_POST['email']);
    
    // Het wachtwoord wordt ongewijzigd opgehaald omdat we het later vergelijken met
    // de versleutelde versie in de database. Beveiliging gebeurt via password_verify
    $password = $_POST['password'];

    // Hier maken we een veilige databasevraag om de gebruiker op te halen
    // Door prepare() en execute() te gebruiken, beschermen we tegen SQL-injectie aanvallen
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    // Haal de gebruikersgegevens op als die bestaan in de database
    // Als het e-mailadres niet bestaat, zal $user 'false' zijn
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Controleer of de gebruiker bestaat EN het wachtwoord correct is
    // password_verify vergelijkt het ingevoerde wachtwoord met de versleutelde hash in de database
    if ($user && password_verify($password, $user['password'])) {
        // Bij correcte gegevens slaan we de gebruikers-ID op in de sessie
        // Hierdoor blijft de gebruiker ingelogd tijdens het browsen op de site
        $_SESSION['user_id'] = $user['id'];
        
        // Stuur de gebruiker door naar het dashboard na succesvol inloggen
        header("Location: dashboard.php");
        exit();
    } else {
        // Bij onjuiste gegevens tonen we een algemene foutmelding
        // We vermelden bewust niet of het e-mailadres of wachtwoord fout is
        // Dit is een beveiligingsmaatregel tegen aanvallen waarbij iemand probeert te achterhalen
        // welke e-mailadressen in de database staan
        $error = "Ongeldige e-mail of wachtwoord.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor correcte weergave en responsiviteit -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Inloggen</title>
    
    <!-- CSS-bestanden voor de opmaak van de pagina -->
    <!-- We gebruiken zowel eigen CSS (style.css) als Bootstrap voor een professionele uitstraling -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk met de applicatienaam -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdgedeelte van de pagina met het inlogformulier -->
    <section class="container mt-5">
        <h2 class="text-center">Inloggen</h2>
        
        <!-- Toon foutmelding alleen als er een inlogfout is opgetreden -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Inlogformulier dat gegevens verstuurt naar dezelfde pagina (POST methode) -->
        <!-- De klasse mx-auto centreert het formulier horizontaal op de pagina -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- Invoerveld voor e-mailadres met verplicht (required) attribuut -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <!-- Invoerveld voor wachtwoord (type="password" verbergt de ingevoerde tekst) -->
            <div class="mb-3">
                <label for="password" class="form-label">Wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <!-- Inlogknop die het formulier verstuurt -->
            <!-- w-100 maakt de knop even breed als het formulier -->
            <button type="submit" class="btn btn-primary w-100">Inloggen</button>
            
            <!-- Links naar andere pagina's voor gebruikers zonder account of met vergeten wachtwoord -->
            <p class="mt-2 text-center">
                <a href="register.php">Account aanmaken</a> | <a href="forgot_password.php">Wachtwoord vergeten?</a>
            </p>
        </form>
    </section>
    
    <!-- Voettekst met copyright informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript-bestanden voor interactiviteit -->
    <!-- Bootstrap JS voor functionaliteit zoals dropdowns en modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
