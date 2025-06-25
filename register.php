<?php
/**
 * Registratiepagina - StudyMate Event Manager
 * 
 * Deze pagina zorgt voor de registratie van nieuwe gebruikers in het systeem.
 * Het bevat een formulier waar gebruikers hun e-mailadres en wachtwoord kunnen invullen.
 * Na succesvolle registratie wordt de gebruiker doorgestuurd naar de inlogpagina.
 */

// Importeert alle benodigde functies uit het functions.php bestand
require_once 'functions.php';

// Controleert of de gebruiker al is ingelogd
// Als de gebruiker al is ingelogd, heeft registreren geen zin meer
// De functie isLoggedIn() komt uit functions.php en controleert de sessiegegevens
if (isLoggedIn()) {
    // Stuurt de gebruiker door naar het dashboard als ze al zijn ingelogd
    header("Location: dashboard.php");
    exit(); // Stopt de uitvoering van de rest van het script
}

// Dit gedeelte wordt alleen uitgevoerd als het formulier is verzonden (als er op de registreerknop is geklikt)
// $_SERVER['REQUEST_METHOD'] controleert of het formulier via POST is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haalt het ingevulde e-mailadres op uit het formulier en maakt het veilig
    // sanitizeInput() is een functie die schadelijke code verwijdert (bijv. om XSS-aanvallen te voorkomen)
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // Haalt het wachtwoord op uit het formulier
    // Het wachtwoord wordt niet gesanitized omdat het later versleuteld wordt opgeslagen
    $password = $_POST['password'] ?? '';

    // Validatie: controleert of het e-mailadres een geldig formaat heeft
    // filter_var met FILTER_VALIDATE_EMAIL controleert of het e-mailadres correct is opgebouwd
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Als het e-mailadres ongeldig is, wordt een foutmelding ingesteld
        $error = "Ongeldig e-mailadres.";
    } 
    // Validatie: controleert of het wachtwoord minimaal 8 tekens lang is
    // strlen() telt het aantal tekens in een string
    elseif (strlen($password) < 8) {
        // Als het wachtwoord te kort is, wordt een foutmelding ingesteld
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } 
    // Als alle validaties zijn geslaagd, wordt het account aangemaakt
    else {
        // try-catch blok vangt database-fouten op voor betere foutafhandeling
        try {
            // Versleutelt het wachtwoord met een one-way hash functie voor veilige opslag
            // password_hash met PASSWORD_BCRYPT is een veilige methode om wachtwoorden te versleutelen
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Voorbereidt een SQL-query om de nieuwe gebruiker in de database op te slaan
            // Prepared statements voorkomen SQL-injecties door parameters apart te houden van de query
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            
            // Voert de SQL-query uit met de gebruikersgegevens
            // De vraagtekens in de query worden vervangen door de waarden in de array
            $stmt->execute([$email, $hashed_password]);
            
            // Maakt een succesmelding aan die op de volgende pagina wordt getoond
            // setFlashMessage slaat een bericht op in de sessie dat één keer wordt weergegeven
            setFlashMessage('success', 'Registratie succesvol! Log in om te beginnen.');
            
            // Stuurt de gebruiker door naar de inlogpagina na succesvolle registratie
            header("Location: index.php");
            
            // Stopt de verdere uitvoering van het script
            exit();
        } 
        // Vangt databasefouten op, zoals wanneer een e-mailadres al in gebruik is
        catch (PDOException $e) {
            // Toont een gebruiksvriendelijke foutmelding aan de gebruiker
            // De echte technische fout wordt niet getoond voor veiligheidsredenen
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
    <!-- Navigatiebalk bovenaan de pagina met de applicatienaam -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Naam van de applicatie, klikbaar logo -->
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdsectie met het registratieformulier -->
    <section class="container mt-5">
        <h2 class="text-center">Registreren</h2>
        
        <!-- Toont foutmeldingen als er validatiefouten zijn -->
        <!-- isset() controleert of de $error variabele bestaat -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Registratieformulier dat gegevens via POST naar dezelfde pagina stuurt -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- E-mail invoerveld met label -->
            <!-- Bij validatiefouten blijft de eerder ingevoerde waarde behouden -->
            <!-- htmlspecialchars() voorkomt XSS-aanvallen door speciale tekens om te zetten -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            
            <!-- Wachtwoord invoerveld met label -->
            <!-- Type="password" zorgt ervoor dat het wachtwoord als stippen wordt weergegeven -->
            <div class="mb-3">
                <label for="password" class="form-label">Wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <!-- Registratieknop die het formulier verstuurt -->
            <button type="submit" class="btn btn-success w-100">Registreren</button>
            
            <!-- Link terug naar de inlogpagina voor gebruikers die al een account hebben -->
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p>
        </form>
    </section>
    
    <!-- Voettekst met copyright informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript bestanden voor interactiviteit -->
    <!-- Bootstrap JavaScript voor responsieve componenten -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Eigen JavaScript voor aanvullende functionaliteit -->
    <script src="script.js"></script>
</body>
</html>