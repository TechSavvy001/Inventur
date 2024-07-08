<?php
// Verbindung einbinden
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formulardaten extrahieren
    $id = $_POST['id'];
    $ansager = $_POST['ansager'];
    $schreiber = $_POST['schreiber'];
    $filiale = $_POST['filiale'];
    
    // Überprüfen, von welcher Seite die Anfrage kam
    $previous_page = $_POST['previous_page'];

    // SQL-Abfrage zum Aktualisieren der Daten
    $stmt = $conn->prepare("UPDATE listen SET ansager = ?, schreiber = ?, filiale = ? WHERE id = ?");
    $stmt->bind_param("sssi", $ansager, $schreiber, $filiale, $id);

    if ($stmt->execute() === TRUE) {
        // Zurück zur vorherigen Seite
        header("Location: $previous_page?liste_id=$id");
        exit();
    } else {
        echo "Fehler: " . $stmt->error;
    }

    // Statement schließen
    $stmt->close();
} else {
    // GET-Anfrage behandeln oder weiterleiten
    echo "Diese Seite sollte nur über ein Formular aufgerufen werden.";
}

// Verbindung schließen
$conn->close();
?>
