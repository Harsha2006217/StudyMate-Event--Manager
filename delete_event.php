<?php
require_once 'functions.php';
requireLogin();

$id = $_GET['id'] ?? '';
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
header("Location: dashboard.php");
?>