<?php
/**
 * "Wachtwoord Vergeten" Pagina
 * 
 * Dit bestand zorgt ervoor dat gebruikers die hun wachtwoord zijn vergeten een nieuw wachtwoord 
 * kunnen aanvragen. De gebruiker vult zijn e-mailadres in, en dan wordt er een speciale link
 * gemaakt waarmee het wachtwoord opnieuw kan worden ingesteld.
 * 
 * In een echte website zou er een e-mail worden verstuurd, maar hier laten we gewoon de link zien.
 */

// Hier laden we alle hulpfuncties in die we nodig hebben
// Dit bestand bevat functies zoals isLoggedIn(), sanitizeInput() en andere handige functies
require_once 'functions.php';

// Hier controleren we of de gebruiker al is ingelogd
// Als iemand al is ingelogd, heeft hij geen wachtwoord reset nodig
// We sturen hem dan direct door naar zijn persoonlijke pagina (dashboard)
if (isLoggedIn()) {
    // Deze regel stuurt de browser door naar een andere pagina
    header("Location: dashboard.php");
    // Deze regel zorgt dat de code hierna niet meer wordt uitgevoerd
    exit();
}

// Deze code wordt alleen uitgevoerd als iemand op de knop "Resetlink aanvragen" heeft gedrukt
// $_SERVER['REQUEST_METHOD'] vertelt ons of het formulier is verzonden (POST) of niet (GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // We halen het e-mailadres op dat de gebruiker heeft ingevuld
    // sanitizeInput() zorgt ervoor dat er geen schadelijke code in het e-mailadres kan zitten
    // Dit is belangrijk voor de veiligheid van de website
    $email = sanitizeInput($_POST['email']);
    
    // Hier maken we een speciale code (token) die we alleen aan deze gebruiker geven
    // Deze code wordt gebruikt in de resetlink om te controleren of de juiste persoon de link gebruikt
    $token = generateResetToken();
    
    // We stellen in dat de resetlink maar 1 uur geldig is
    // Na dat uur kan de link niet meer gebruikt worden voor veiligheid
    // strtotime('+1 hour') berekent de tijd over 1 uur vanaf nu
    // date() zet deze tijd om in een formaat dat de database begrijpt
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Nu slaan we de resetcode en de vervaltijd op in de database
    // We doen dit alleen voor de gebruiker met het opgegeven e-mailadres
    // De ? tekens zijn plaatshouders voor de waarden die we veilig willen invoegen
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
    // Hier voeren we de opdracht uit met de echte waarden
    $stmt->execute([$token, $expiry, $email]);

    // Nu maken we een bericht voor de gebruiker dat de resetlink is verstuurd
    // In een echte website zou er nu een e-mail verstuurd worden, maar hier simuleren we dat
    // We tonen de link direct op het scherm (in een echte website zou je dit niet doen!)
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