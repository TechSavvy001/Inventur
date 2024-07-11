<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aufnahmelisten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }

    include '../config/config.php';

    // Listen ID abrufen
    $liste_id = $_GET['liste_id'] ?? null;

    // SQL-Abfrage zum Abrufen der neuesten Benutzerdaten
    $sql = "SELECT ansager, schreiber, filiale, benutzer, listeNummer FROM listen WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $liste_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prüfen, ob die Abfrage Ergebnisse zurückgibt
    if ($result->num_rows > 0) {
        // Daten der ersten Zeile abrufen
        $userDetails = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Keine Benutzerdaten gefunden</div>";
        exit; // Stoppe die Ausführung, wenn keine Benutzerdaten gefunden werden
    }

    // SQL-Abfrage zum Abrufen der Fahrzeugdaten
    $sql = "SELECT * FROM fahrzeuge WHERE liste_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $liste_id);
    $stmt->execute();
    $vehicles = $stmt->get_result();

    // Verbindung schließen
    $conn->close();
    ?>
    <div class="container mt-5">

    <nav class="menubar bg-white shadow-sm py-2 px-4">
        <div class="container-fluid">
            <h1>Inventur-Aufnahmelisten</h1>
        </div>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="content bg-white p-4 rounded shadow-sm mt-4">
                <h3 class="mb-3">Benutzerdetails</h3>
                <div id="user-details">
                    <p>Ansager: <b><?php echo htmlspecialchars($userDetails['ansager']); ?></b></p>
                    <p>Schreiber: <b><?php echo htmlspecialchars($userDetails['schreiber']); ?></b></p>
                    <p>Filiale: <b><?php echo htmlspecialchars($userDetails['filiale']); ?></b></p>
                    <p>Benutzer: <b><?php echo htmlspecialchars($userDetails['benutzer']); ?></b></p>
                    <p>Liste-Nummer: <b><?php echo htmlspecialchars($userDetails['listeNummer']); ?></b></p>
                </div>
                <div class="actions mt-3">
                    <a href="erfassen.php?liste_id=<?php echo $liste_id; ?>" class="btn btn-primary">Neues Fahrzeug</a>
                    <a href="../controllers/edit_list.php?id=<?php echo $liste_id; ?>" class="btn btn-secondary">Bearbeiten</a>
                </div>
            </div>
        </div>
    </div>
   
    <div class="row mt-4">
        <div class="col-12">
            <div class="content">
                <div class="p-3 mb-4 bg-white rounded shadow-sm">
                    <h2>Fahrzeuge</h2>
                    <?php if ($vehicles->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Barcode 8-stellig</th>
                                        <th>Abteilung</th>
                                        <th>Fahrgestellnummer</th>
                                        <th>Marke</th>
                                        <th>Modell</th>
                                        <th>Farbe</th>
                                        <th>Aufnahmebereich</th>
                                        <th>Bild Nummer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($vehicle = $vehicles->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($vehicle['barcode']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['barcode8']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['abteilung']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['fgNummer']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['marke']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['modell']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['farbe']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['aufnahmebereich']); ?></td>
                                            <td><?php echo htmlspecialchars($vehicle['bildNummer']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="alert alert-warning">Keine Fahrzeuge gefunden.</p>
                    <?php endif; ?>
                    <div class="mt-4">
                        <a href="auswahl.php" class="btn btn-primary">Go Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>

</html>
