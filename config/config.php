<?php

// Variablen festlegen für die Datenbank
$servername = "localhost";      // Servername
$username = "root";             // Benutzername für die Datenbankverbindung
$password = "";                 // Passwort für den Datenbankbenutzer; hier leer --> keine Sicherheit in einem echten Projekt!
$dbname = "inventur";           // Name der Datenbank, zu der eine Verbindung hergestellt werden soll

// Verbindung herstellen
$conn = new mysqli($servername, $username, $password, $dbname);         // Speichert das Verbindungsobjekt

// Verbindung prüfen
if ($conn->connect_error) {             // Prüfung, ob es bei der Verbindung Fehler gibt
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/Inventur/'); // Basis-URL relativ zur Root-Domain, passe dies bei Bedarf an
}
