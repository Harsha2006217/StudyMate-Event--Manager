<?php
session_start();
require_once 'db_connect.php';

// Controleer of gebruiker is ingelogd
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect naar login als niet ingelogd
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

// Valideer en reinig invoer
function sanitizeInput(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Genereer reset-token
function generateResetToken(): string {
    return bin2hex(random_bytes(16));
}

// Toon tijdelijke melding
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>