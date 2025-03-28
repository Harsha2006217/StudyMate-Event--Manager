<?php
require_once 'functions.php';

// Vernietig sessie en redirect
session_destroy();
$_SESSION = []; // Leeg sessie-array voor zekerheid
header("Location: index.php");
exit();
?>