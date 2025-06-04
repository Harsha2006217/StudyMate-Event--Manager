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
    
    // We halen het e-mailadres op en maken het veilig met sanitizeInput()
    // sanitizeInput() zorgt ervoor dat er geen schadelijke code in kan zitten
    $email = sanitizeInput($_POST['email']);
    
    // We halen het wachtwoord op maar maken het NIET veilig met sanitizeInput()
    // Dit is omdat we het wachtwoord gaan vergelijken met een versleutelde versie
    // Als we sanitizeInput zouden gebruiken, zou de vergelijking niet werken
    $password = $_POST['password'];

    // Nu gaan we in de database zoeken naar een gebruiker met dit e-mailadres
    // We maken eerst een veilige zoekopdracht (query) klaar met prepare()
    // Het vraagteken ? is een plaatshouder voor het e-mailadres dat we veilig willen invoegen
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    
    // Nu voeren we de zoekopdracht uit en vullen het e-mailadres in op de plaats van ?
    $stmt->execute([$email]);
    
    // We halen de gevonden gebruiker op (als die bestaat)
    // FETCH_ASSOC betekent dat we de gegevens krijgen als een lijst met namen en waarden
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nu controleren we of er een gebruiker is gevonden EN of het wachtwoord juist is
    // password_verify controleert of het ingevoerde wachtwoord overeenkomt met de versleutelde versie
    // die in de database staat
    if ($user && password_verify($password, $user['password'])) {
        // Als alles klopt, slaan we de ID van de gebruiker op in de sessie
        // De sessie is een soort tijdelijk geheugen dat de website gebruikt om te onthouden wie je bent
        // Door de user_id op te slaan, weet de website bij volgende pagina's nog steeds wie er is ingelogd
        $_SESSION['user_id'] = $user['id'];
        
        // We sturen de gebruiker door naar het dashboard (de hoofdpagina na inloggen)
        header("Location: dashboard.php");
        
        // We stoppen de uitvoering van de code hier, omdat de gebruiker toch wordt doorgestuurd
        exit();
    } else {
        // Als het e-mailadres niet bestaat OF het wachtwoord klopt niet, maken we een foutmelding
        // We zeggen bewust niet precies wat er mis is (voor de veiligheid)
        // Dit voorkomt dat iemand kan achterhalen welke e-mailadressen in de database staan
        $error = "Ongeldige e-mail of wachtwoord.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Deze regels geven informatie aan de browser over hoe de pagina moet worden weergegeven -->
    <!-- charset="UTF-8" zorgt ervoor dat speciale tekens (zoals é, ë, ç) goed worden weergegeven -->
    <meta charset="UTF-8">
    
    <!-- Dit zorgt ervoor dat de website er goed uitziet op mobiele telefoons -->
    <!-- Het past de grootte van de website aan aan de grootte van het scherm -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- De titel die bovenaan in het browsertabblad wordt weergegeven -->
    <title>StudyMate - Inloggen</title>
    
    <!-- Hier laden we de opmaakbestanden (CSS) in die bepalen hoe de website eruitziet -->
    <!-- style.css is ons eigen opmaakbestand -->
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