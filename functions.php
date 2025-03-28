<?php
session_start();
require_once 'db_connect.php';

// Controleer of gebruiker is ingelogd
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect naar login als niet ingelogd
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

// Valideer en reinig invoer
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Genereer reset-token
function generateResetToken() {
    return bin2hex(random_bytes(16));
}
?>