<?php

// Sitzung starten und Überprüfung der Anmeldung
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../public/login.php');
    exit;
}

// Konfigurationsdatei einbinden
include '../config/config.php';

if (isset($_GET['id'])) {           // Überprüft, ob eine Listen-ID als URL-Parameter übergeben wurde
    $liste_id = $_GET['id'];        // Listen-ID in einer Variablen speichen

    // Überprüfen, ob die Liste dem Benutzer gehört
    $username = $_SESSION['username'];                                                  // Benutzername aus der Sitzung speichern 
    $stmt = $conn->prepare("SELECT * FROM listen WHERE id = ? AND benutzer = ?");       // SQL-Abfrage, ob die Liste dem aktuellen Benutzer gehört
    $stmt->bind_param("is", $liste_id, $username);                                      // Parameter an die SQL-Abfrage binden
    $stmt->execute();                                                                   // Vorbereitete Abfrage ausführen
    $result = $stmt->get_result();                                                      // Ergebnis der Abfrage holen

    if ($result->num_rows > 0) {                                                        // Überprüfung, ob die Abfrage eine Zeile zurückgegeben hat, d.h. ob Liste dem Benutzer gehört
        // Alle zu der Liste zugehörigen Fahrzeuge löschen                                           
        $stmt = $conn->prepare("DELETE FROM fahrzeuge WHERE liste_id = ?");
        $stmt->bind_param("i", $liste_id);
        if ($stmt->execute()) {
            // Liste löschen
            $stmt = $conn->prepare("DELETE FROM listen WHERE id = ?");
            $stmt->bind_param("i", $liste_id);
            if ($stmt->execute()) {
                header('Location: ../public/listen_bearbeiten.php');
                exit();
            } else {
                echo "Fehler beim Löschen der Liste.";
            }
        } else {
            echo "Fehler beim Löschen der zugehörigen Fahrzeuge.";
        }
    } else {
        echo "Keine Berechtigung zum Löschen dieser Liste.";
    }
} else {
    echo "Keine Listen-ID angegeben.";
}

$conn->close();
?>