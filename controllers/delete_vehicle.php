<?php

// Sitzung starten und Überprüfung der Anmeldung
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../public/login.php');
    exit;
}

// Konfigurationsdatei einbinden
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $vehicle_id = $_POST['id'];

    // Fahrzeug und zugehörigen Bildpfad abrufen
    $stmt = $conn->prepare("SELECT bildPfad FROM fahrzeuge WHERE id = ?");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();

    if ($vehicle) {
        // Bild löschen, wenn es existiert
        if (!empty($vehicle['bildPfad']) && file_exists($vehicle['bildPfad'])) {
            unlink($vehicle['bildPfad']);
        }

        // Fahrzeug aus der Datenbank löschen
        $stmt = $conn->prepare("DELETE FROM fahrzeuge WHERE id = ?");
        $stmt->bind_param("i", $vehicle_id);

        if ($stmt->execute() === TRUE) {
            echo "Fahrzeug erfolgreich gelöscht.";
        } else {
            echo "Fehler beim Löschen des Fahrzeugs: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Fahrzeug nicht gefunden.";
    }
} else {
    echo "Ungültige Anfrage.";
}

$conn->close();
?>
