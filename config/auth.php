<?php
// Startet die Session
session_start();

// Überprüft, ob der Benutzer angemeldet ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Wenn der Benutzer nicht angemeldet ist, wird er zur Login-Seite weitergeleitet
    header('Location: ../views/user/login.php');
    exit; // Beendet das Skript, um sicherzustellen, dass der Rest der Seite nicht geladen wird
}
?>
