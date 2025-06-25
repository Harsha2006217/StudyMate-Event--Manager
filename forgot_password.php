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

// Importeren van benodigde functies uit functions.php
// Dit is essentieel voor de werking van de wachtwoord reset functionaliteit
require_once 'functions.php';

// Beveiligingsmaatregel: Controleert of de gebruiker al is ingelogd
// Als de gebruiker al ingelogd is, heeft deze geen wachtwoord reset nodig
// De isLoggedIn() functie controleert de sessie op een geldig gebruiker-ID
if (isLoggedIn()) {
    // Redirect naar dashboard om gebruiker naar zijn persoonlijke omgeving te sturen
    // Dit voorkomt onnodige wachtwoord resets voor ingelogde gebruikers
    header("Location: dashboard.php");
    // Exit zorgt ervoor dat de rest van de code niet wordt uitgevoerd
    // Dit is belangrijk voor de beveiliging, omdat anders de code hieronder alsnog zou kunnen worden uitgevoerd
    exit();
}

// Verwerking van het formulier wanneer gebruiker op "Resetlink aanvragen" drukt
// REQUEST_METHOD === 'POST' betekent dat het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Opvangen en opschonen van het ingevoerde e-mailadres
    // sanitizeInput() verwijdert mogelijk schadelijke code uit de invoer (XSS-preventie)
    $email = sanitizeInput($_POST['email']);
    
    // Genereren van een unieke reset-token voor deze specifieke wachtwoord reset
    // Deze token zal in de database worden opgeslagen en in de resetlink worden gebruikt
    $token = generateResetToken();
    
    // Instellen van een verloopdatum voor de token (1 uur vanaf nu)
    // Deze tijdslimiet is een beveiligingsmaatregel om misbruik van oude resetlinks te voorkomen
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Database update: sla de token en verloopdatum op bij de juiste gebruiker
    // Prepared statement wordt gebruikt om SQL-injectie aanvallen te voorkomen
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]);

    // Bevestiging naar de gebruiker dat de resetlink is verzonden
    // In een productieomgeving zou hier een echte e-mail worden verzonden
    // setFlashMessage slaat een bericht op dat op de volgende pagina wordt getoond
    setFlashMessage('success', "Controleer je e-mail voor de resetlink (simulatie: reset_password.php?token=$token).");
    
    // Na succesvol aanmaken van de reset-token wordt de gebruiker teruggestuurd naar de inlogpagina
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-tags voor juiste karakterset en responsief ontwerp -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Wachtwoord vergeten</title>
    
    <!-- Externe CSS-bestanden voor consistent ontwerp door de hele applicatie -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigatiebalk met alleen de applicatienaam voor eenvoudige navigatie -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
        </div>
    </nav>
    
    <!-- Hoofdgedeelte met het wachtwoord reset formulier -->
    <section class="container mt-5">
        <h2 class="text-center">Wachtwoord vergeten</h2>
        
        <!-- Formulier dat bij verzending een POST-verzoek stuurt naar dezelfde pagina -->
        <form method="POST" class="col-md-6 mx-auto">
            <!-- E-mailadres invoerveld met required attribuut om lege inzendingen te voorkomen -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <!-- Verzendknop voor het formulier met duidelijke actie-omschrijving -->
            <button type="submit" class="btn btn-primary w-100">Resetlink aanvragen</button>
            
            <!-- Terugkeeroptie voor gebruikers die zich hun wachtwoord toch herinneren -->
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p>
        </form>
    </section>
    
    <!-- Footer met copyright informatie voor consistente pagina-afsluiting -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    
    <!-- JavaScript-bestanden voor interactieve elementen en Bootstrap-functionaliteit -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
