<?php
require_once 'functions.php';
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ongeldig e-mailadres.";
    } elseif (strlen($password) < 8) {
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashed_password]);
            setFlashMessage('success', 'Registratie succesvol! Log in om te beginnen.');
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "E-mailadres bestaat al.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Registreren</title>
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
        <h2 class="text-center">Registreren</h2>
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