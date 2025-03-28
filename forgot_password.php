<?php
require_once 'functions.php';
if (isLoggedIn()) header("Location: dashboard.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $token = generateResetToken();
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]);

    // Hier zou normaal een e-mail worden verzonden met de link: reset_password.php?token=$token
    $message = "Controleer je e-mail voor de resetlink (simulatie: reset_password.php?token=$token).";
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>StudyMate - Wachtwoord vergeten</title>
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
        <h2 class="text-center">Wachtwoord vergeten</h2>
        <?php if (isset($message)) echo "<p class='text-success text-center'>$message</p>"; ?>
        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Resetlink aanvragen</button>
            <p class="mt-2 text-center"><a href="index.php">Terug naar inloggen</a></p>
        </form>
    </section>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 StudyMate Event Manager</p>
    </footer>
</body>
</html>