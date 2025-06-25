<?php
/**
 * Registratiepagina - StudyMate Event Manager
 * 
 * Deze pagina zorgt voor de registratie van nieuwe gebruikers in het systeem.
 * Het bevat een formulier waar gebruikers hun e-mailadres en wachtwoord kunnen invullen.
 * Na succesvolle registratie wordt de gebruiker doorgestuurd naar de inlogpagina.
 */

// Importeert alle benodigde functies uit het functions.php bestand
require_once 'functions.php'; // Zorgt ervoor dat alle functies uit functions.php beschikbaar zijn

// Controleert of de gebruiker al is ingelogd
if (isLoggedIn()) { 
    // Als de gebruiker al is ingelogd, wordt hij doorgestuurd naar het dashboard
    header("Location: dashboard.php");
    exit(); // Voorkomt dat de rest van de code wordt uitgevoerd
}

// Controleert of het formulier is verzonden via een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haalt het ingevulde e-mailadres op en maakt het veilig
    $email = sanitizeInput($_POST['email'] ?? ''); // Voorkomt XSS-aanvallen door invoer te saneren
    
    // Haalt het wachtwoord op uit het formulier
    $password = $_POST['password'] ?? ''; // Wachtwoord wordt niet gesanitized omdat het later versleuteld wordt

    // Controleert of het e-mailadres een geldig formaat heeft
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ongeldig e-mailadres."; // Foutmelding bij ongeldig e-mailadres
    } 
    // Controleert of het wachtwoord minimaal 8 tekens lang is
    elseif (strlen($password) < 8) {
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn."; // Foutmelding bij te kort wachtwoord
    } 
    // Als validaties slagen, wordt het account aangemaakt
    else {
        try {
            // Versleutelt het wachtwoord voor veilige opslag
            $hashed_password = password_hash($password, PASSWORD_BCRYPT); // BCRYPT is een veilige hashing-methode
            
            // Bereidt een SQL-query voor om de gebruiker op te slaan
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)"); // Voorkomt SQL-injecties
            
            // Voert de SQL-query uit
            $stmt->execute([$email, $hashed_password]); // Voegt de gebruiker toe aan de database
            
            // Stelt een succesmelding in en stuurt de gebruiker door naar de inlogpagina
            setFlashMessage('success', 'Registratie succesvol! Log in om te beginnen.');
            header("Location: index.php");
            exit(); // Voorkomt verdere uitvoering van de code
        } 
        catch (PDOException $e) {
            $error = "E-mailadres bestaat al."; // Foutmelding bij een databasefout, zoals een bestaand e-mailadres
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8"> <!-- Zorgt ervoor dat speciale tekens correct worden weergegeven -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Zorgt voor responsiviteit -->
    <title>StudyMate - Registreren</title> <!-- Titel van de pagina -->
    <link rel="stylesheet" href="style.css"> <!-- Eigen stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a> <!-- Applicatienaam -->
        </div>
    </nav>
    
    <section class="container mt-5">
        <h2 class="text-center">Registreren</h2>
        
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p> <!-- Toont foutmeldingen -->
        <?php endif; ?>
        
        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required> <!-- E-mail invoerveld -->
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Wachtwoord</label>
                <input type="password" class="form-control" id="password" name="password" required> <!-- Wachtwoord invoerveld -->
            </div>
            
            <button type="submit" class="btn btn-success w-100">Registreren</button> <!-- Registratieknop -->
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p> <!-- Link naar inlogpagina -->
        </form>
    </section>
    
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p> <!-- Voettekst -->
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS -->
    <script src="script.js"></script> <!-- Eigen JavaScript -->
</body>
</html>
