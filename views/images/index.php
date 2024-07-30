<?php
// Startet die Session, falls noch nicht geschehen
session_start();

// Überprüfe, ob die PHP-Sitzung noch nicht gestartet wurde, und starte sie, falls nötig
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Wenn der Benutzer nicht eingeloggt ist, leite ihn zur Login-Seite weiter
    header('Location: ' . BASE_URL . 'views/users/login.php');
    exit;
}

$title = "Gespeicherte Bilder";
include_once __DIR__ . '/../../config/config.php';
include BASE_PATH . 'views/layouts/header.php';

// Benutzername aus der Session abrufen
$username = $_SESSION['username'];

// SQL-Abfrage, um alle Bildnummern und Fahrzeugdetails des aktuellen Benutzers abzurufen
$sql = "SELECT l.listeNummer, f.bildPfad, f.barcode8, f.fgNummer, f.marke, f.modell, f.farbe 
        FROM fahrzeuge f
        JOIN listen l ON f.liste_id = l.id
        WHERE l.benutzer = ?
        ORDER BY l.listeNummer";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$vehicles = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gespeicherte Bilder</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/css.css">
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
        .list-section {
            margin-bottom: 30px;
        }
        .list-section h2 {
            text-align: center;
            margin-bottom: 20px;
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
                <?php
                if (isset($vehicles) && count($vehicles) > 0) {
                    $currentList = null;
                    foreach ($vehicles as $row) {
                        if ($currentList !== $row['listeNummer']) {
                            if ($currentList !== null) {
                                echo "</div></div>"; // Schließe vorherige Liste
                            }
                            $currentList = $row['listeNummer'];
                            echo "<div class='list-section'>";
                            echo "<h2>Liste: " . htmlspecialchars($currentList) . "</h2>";
                            echo "<div class='d-flex flex-wrap justify-content-center'>";
                        }

                        $bildPfad = htmlspecialchars($row['bildPfad']);
                        if (file_exists(BASE_PATH . '/' . $bildPfad)) {
                            echo "<div class='image-card'>";
                            echo "<img src='" . BASE_URL . $bildPfad . "' alt='Bild'>";
                            echo "<p><strong>Barcode8:</strong> " . htmlspecialchars($row['barcode8']) . "</p>";
                            echo "<p><strong>Fgst-Nr:</strong> " . htmlspecialchars($row['fgNummer']) . "</p>";
                            echo "<p><strong>Marke:</strong> " . htmlspecialchars($row['marke']) . "</p>";
                            echo "<p><strong>Modell:</strong> " . htmlspecialchars($row['modell']) . "</p>";
                            echo "<p><strong>Farbe:</strong> " . htmlspecialchars($row['farbe']) . "</p>";
                            echo "</div>";
                        } else {
                            echo "<div class='image-card'>";
                            echo "<p>Bild nicht gefunden: $bildPfad</p>";
                            echo "</div>";
                        }
                    }
                    echo "</div></div>"; // Schließe letzte Liste
                } else {
                    echo "Keine Bilder gefunden.";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap-JavaScript-Dateien einbinden -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>