<?php
/**
 * Inlogpagina voor StudyMate Event Manager
 * 
 * Dit is de startpagina van de applicatie waar gebruikers kunnen inloggen.
 * De pagina bevat een inlogformulier en links naar registratie en wachtwoordherstel.
 * Als de gebruiker al is ingelogd, wordt hij doorgestuurd naar het dashboard.
 */

// Laad de benodigde functies
require_once 'functions.php'; // Dit zorgt ervoor dat alle functies uit 'functions.php' beschikbaar zijn.

// Controleer of de gebruiker al is ingelogd; stuur door naar dashboard indien ja
if (isLoggedIn()) { 
    // Controleert of de gebruiker een actieve sessie heeft. Zo ja, dan wordt hij doorgestuurd naar het dashboard.
    header("Location: dashboard.php"); // Verwijst de gebruiker naar het dashboard.
    exit(); // Stopt verdere uitvoering van de code.
}

// Verwerk het inlogformulier wanneer de gebruiker op de inlogknop klikt
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    // Controleert of het formulier is verzonden via de POST-methode.

    $email = sanitizeInput($_POST['email']); 
    // Haalt het ingevoerde e-mailadres op en maakt het veilig tegen schadelijke invoer.

    $password = $_POST['password']; 
    // Haalt het ingevoerde wachtwoord op. Dit wordt later vergeleken met de versleutelde versie in de database.

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?"); 
    // Bereidt een SQL-query voor om de gebruiker op te halen op basis van het e-mailadres.
    $stmt->execute([$email]); 
    // Voert de query uit met het opgegeven e-mailadres.

    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
    // Haalt de gegevens van de gebruiker op als deze bestaat. Anders is $user 'false'.

    if ($user && password_verify($password, $user['password'])) { 
        // Controleert of de gebruiker bestaat en of het wachtwoord correct is.
        $_SESSION['user_id'] = $user['id']; 
        // Slaat de gebruikers-ID op in de sessie om de gebruiker ingelogd te houden.
        header("Location: dashboard.php"); 
        // Verwijst de gebruiker naar het dashboard na succesvol inloggen.
        exit(); 
    } else {
        $error = "Ongeldige e-mail of wachtwoord."; 
        // Geeft een algemene foutmelding bij onjuiste gegevens.
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta-informatie voor correcte weergave en responsiviteit -->
    <meta charset="UTF-8"> <!-- Stelt de tekencodering in op UTF-8. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Zorgt voor een responsieve weergave. -->
    <title>StudyMate - Inloggen</title> <!-- Titel van de pagina. -->

    <!-- CSS-bestanden voor de opmaak van de pagina -->
    <link rel="stylesheet" href="style.css"> <!-- Link naar de eigen CSS-bestand. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <!-- Link naar Bootstrap CSS voor een professionele uitstraling. -->
</head>
<body>
    <!-- Navigatiebalk met de applicatienaam -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a> <!-- Link naar de homepage. -->
        </div>
    </nav>
    
    <!-- Hoofdgedeelte van de pagina met het inlogformulier -->
    <section class="container mt-5">
        <h2 class="text-center">Inloggen</h2> <!-- Koptekst van het inlogformulier. -->
        
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p> 
            <!-- Toont een foutmelding als er een inlogfout is opgetreden. -->
        <?php endif; ?>
        
        <form method="POST" class="col-md-6 mx-auto"> 
            <!-- Inlogformulier dat gegevens verstuurt naar dezelfde pagina via de POST-methode. -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label> 
                <!-- Label voor het e-mailadres. -->
                <input type="email" class="form-control" id="email" name="email" required> 
                <!-- Invoerveld voor e-mailadres. Het 'required'-attribuut maakt het verplicht. -->
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Wachtwoord</label> 
                <!-- Label voor het wachtwoord. -->
                <input type="password" class="form-control" id="password" name="password" required> 
                <!-- Invoerveld voor wachtwoord. Het 'required'-attribuut maakt het verplicht. -->
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Inloggen</button> 
            <!-- Knop om het formulier te versturen. -->
            
            <p class="mt-2 text-center">
                <a href="register.php">Account aanmaken</a> | <a href="forgot_password.php">Wachtwoord vergeten?</a> 
                <!-- Links naar registratie en wachtwoordherstel. -->
            </p>
        </form>
    </section>
    
    <!-- Voettekst met copyright informatie -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p> <!-- Copyright informatie. -->
    </footer>
    
    <!-- JavaScript-bestanden voor interactiviteit -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
    <!-- Bootstrap JS voor functionaliteit zoals dropdowns en modals. -->
    <script src="script.js"></script> <!-- Link naar eigen JavaScript-bestand. -->
</body>
</html>
