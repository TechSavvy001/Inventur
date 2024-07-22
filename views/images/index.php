<?php
// Startet die Session, falls noch nicht geschehen
session_start();

// Setzt den Seitentitel auf "Bilder"
$title = "Bilder";

// Binden den Header ein, der wahrscheinlich den HTML-Kopfbereich und grundlegende Layouts enthält
include '../layouts/header.php';

// Binde die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once '../../config/config.php';

// Binde den VehicleController ein, um Fahrzeugdaten zu verwalten
include_once '../../controllers/VehicleController.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gespeicherte Bilder</title>
    <link rel="stylesheet" href="../../public/assets/css/css.css">
    <style>
        .image-card {
            flex: 1 1 200px; /* Flexible Box mit einer Basisbreite von 200px */
            max-width: 250px; /* Maximalbreite */
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
        }
        .image-card img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .image-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
            <h1>Gespeicherte Bilder</h1>
        </div>
        <div class="container mt-5">
            <div class="content bg-white p-4 rounded shadow-sm">

                <div class="d-flex flex-wrap justify-content-center">
                    <?php
                    // Binde die Konfigurationsdatei ein, falls sie nicht bereits eingebunden ist
                    include '../../config/config.php';

                    // SQL-Abfrage, um alle Bildnummern und Fahrzeugdetails abzurufen
                    $sql = "SELECT bildPfad, barcode8, fgNummer, marke, modell, farbe, bildNummer FROM fahrzeuge";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Ausgabe der Bilder
                        while($row = $result->fetch_assoc()) {
                            $bildPfad = htmlspecialchars($row['bildPfad']);

                            // Überprüfen, ob der Bildpfad existiert
                            if (file_exists(dirname(__DIR__, 2) . '/' . $bildPfad)) {
                                echo "<div class='image-card'>";
                                echo "<img src='../../" . $bildPfad . "' alt='Bild'>";
                                echo "<p><strong>Barcode8:</strong> " . htmlspecialchars($row['barcode8']) . "</p>";
                                echo "<p><strong>Fgst-Nr:</strong> " . htmlspecialchars($row['fgNummer']) . "</p>";
                                echo "<p><strong>Marke:</strong> " . htmlspecialchars($row['marke']) . "</p>";
                                echo "<p><strong>Modell:</strong> " . htmlspecialchars($row['modell']) . "</p>";
                                echo "<p><strong>Farbe:</strong> " . htmlspecialchars($row['farbe']) . "</p>";
                                echo "<p><strong>BildNr:</strong> " . htmlspecialchars($row['bildNummer']) . "</p>";
                                echo "</div>";
                            } else {
                                echo "<div class='image-card'>";
                                echo "<p>Bild nicht gefunden: $bildPfad</p>";
                                echo "</div>";
                            }
                        }
                    } else {
                        echo "Keine Bilder gefunden.";
                    }

                    // Schließen der Datenbankverbindung
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap-JavaScript-Dateien einbinden -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
