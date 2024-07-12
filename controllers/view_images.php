<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gespeicherte Bilder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css.css"> 
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
    <h1 class="text-center">Gespeicherte Bilder</h1>
    <div class="d-flex flex-wrap justify-content-center">
        <?php
        // Verbindung einbinden
        include '../config/config.php';

        // Abfrage, um alle Bildnummern und Fahrzeugdetails abzurufen
        $sql = "SELECT bildPfad, barcode, fgNummer, marke, modell, farbe, bildNummer FROM fahrzeuge";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Ausgabe der Bilder
            while($row = $result->fetch_assoc()) {
                $bildPfad = htmlspecialchars($row['bildPfad']);

                // Überprüfen Sie, ob der Bildpfad existiert
                if (file_exists("../" . $bildPfad)) {
                    echo "<div class='image-card'>";
                    echo "<img src='../" . $bildPfad . "' alt='Bild'>";
                    echo "<p><strong>Barcode:</strong> " . htmlspecialchars($row['barcode']) . "</p>";
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

        // Verbindung schließen
        $conn->close();
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
