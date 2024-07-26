<?php
// Startet die Session
session_start();

// Setzt den Seitentitel auf "Listen Details"
$title = "Listen Details";

// Binde die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once __DIR__ . '/../../config/config.php';

// Bindet den Header ein, der wahrscheinlich den HTML-Kopfbereich und grundlegende Layouts enthält
include BASE_PATH . 'views/layouts/header.php';

// Bindet den ListController ein, um Listen zu verwalten
include_once BASE_PATH . 'controllers/ListController.php';

// Überprüft, ob eine Listen-ID in der URL übergeben wurde
if (!isset($_GET['liste_id'])) {
    echo "Keine Listen-ID angegeben.";
    exit;
}

// Holt die Listen-ID aus der URL
$liste_id = $_GET['liste_id'];

// Initialisiert den ListController mit der Datenbankverbindung
$listController = new ListController($conn);

// Holt die Details der Liste anhand der Listen-ID
$listDetails = $listController->getListDetails($liste_id);

// Wenn die Liste nicht gefunden wird, wird eine Fehlermeldung angezeigt
if (!$listDetails) {
    echo "Liste nicht gefunden.";
    exit;
}

// Holt die Fahrzeuge, die zu dieser Liste gehören
$vehicles = $listController->getVehiclesByListId($liste_id);

// Erfolgsmeldung abfangen
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listen Details</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/css.css">
     <style>
        .table-responsive {
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-secondary {
            margin-right: 5px;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
    <nav class="menubar bg-white shadow-sm py-2 px-4">
        <div class="container-fluid">
            <h1>Inventur-Aufnahmelisten</h1>
        </div>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="content bg-white p-4 rounded shadow-sm mt-4">
                <h3 class="mb-3">Benutzerdetails</h3>
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                <div id="user-details">
                    <p>Ansager: <b><?php echo htmlspecialchars($listDetails['ansager']); ?></b></p>
                    <p>Schreiber: <b><?php echo htmlspecialchars($listDetails['schreiber']); ?></b></p>
                    <p>Filiale: <b><?php echo htmlspecialchars($listDetails['filiale']); ?></b></p>
                    <p>Benutzer: <b><?php echo htmlspecialchars($listDetails['benutzer']); ?></b></p>
                    <p>Liste-Nummer: <b><?php echo htmlspecialchars($listDetails['listeNummer']); ?></b></p>
                </div>
                <div class="actions mt-3">
                    <a href="<?php echo BASE_URL; ?>vehicles/create?liste_id=<?php echo $liste_id; ?>" class="btn btn-primary">Neues Fahrzeug</a>
                    <a href="<?php echo BASE_URL; ?>lists/edit?id=<?php echo $liste_id; ?>" class="btn btn-secondary">Bearbeiten</a>
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
                        <table class="table table-striped table-hover">
                        <thead>
                                    <tr>
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
                        <a href="<?php echo BASE_URL; ?>lists/index" class="btn btn-primary">Zurück</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
