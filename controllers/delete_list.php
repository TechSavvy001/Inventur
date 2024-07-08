<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../public/login.php');
    exit;
}

include '../config/config.php';

if (isset($_GET['id'])) {
    $liste_id = $_GET['id'];

    // Überprüfen, ob die Liste dem Benutzer gehört
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM listen WHERE id = ? AND benutzer = ?");
    $stmt->bind_param("is", $liste_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Alle zugehörigen Fahrzeuge löschen
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