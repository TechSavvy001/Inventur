<?php
// Verbindung einbinden
include '../config/config.php';

// Abfrage, um alle Bildpfade abzurufen
$sql = "SELECT bildPfad FROM fahrzeuge";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Ausgabe der Bildpfade
    echo "<h1>Gespeicherte Bilder</h1>";
    while($row = $result->fetch_assoc()) {
        $bildPfad = htmlspecialchars($row['bildPfad']);
        echo "<div>";
        echo "<img src='" . $bildPfad . "' alt='Bild' style='max-width: 200px; max-height: 200px; margin: 10px;'>";
        echo "<p>$bildPfad</p>"; // Bildpfad als Text anzeigen
        echo "</div>";
    }
} else {
    echo "Keine Bilder gefunden.";
}

// Verbindung schließen
$conn->close();
?>
