<?php
// Starte die Session
session_start();

// Setze den Seitentitel auf "Liste bearbeiten"
$title = "Liste bearbeiten";

// Binde die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once dirname(__DIR__, 2) . '/config/config.php'; 

// Binde den Header ein, der wahrscheinlich den HTML-Kopfbereich und grundlegende Layouts enthält
include BASE_PATH . 'views/layouts/header.php';

// Binde den ListController ein, um Listen zu verwalten
include_once BASE_PATH . 'controllers/ListController.php';

// Initialisiere den ListController mit der Datenbankverbindung
$listController = new ListController($conn);

// Überprüfe, ob die Listen-ID in der URL angegeben ist
if (!isset($_GET['id'])) {
    echo "Keine Listen-ID angegeben.";
    exit;
}

// Hole die Listen-ID aus der URL
$id = $_GET['id'];

// Hole die Listendetails anhand der Listen-ID
$list = $listController->getById($id);

// Hole die Fahrzeuge, die zu dieser Liste gehören
$vehicles = $listController->getVehiclesByListId($id);

// Speichere die vorherige Seite, um später darauf zurückzukehren
$previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'views/lists/index.php';

// Erfolgsmeldung abfangen und anzeigen
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
    <title>Liste bearbeiten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/css.css">
    <style>
        .alert-success {
            display: <?php echo $success_message ? 'block' : 'none'; ?>;
        }
        .btn-save:hover, .btn-delete:hover {
            opacity: 0.8;
        }        
        .table th, .table td {
            text-align: left;
        }

    </style>
</head>
<body>
<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
    <div class="menubar bg-white shadow-sm py-2 px-4">
        <h1>Liste bearbeiten</h1>
    </div>

    <div class="content bg-white p-4 rounded shadow-sm mt-4">
        <form action="<?php echo BASE_URL; ?>controllers/ListController.php?action=update" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" name="previous_page" value="<?php echo htmlspecialchars($previous_page); ?>">
            <div class="form-group mb-3">
                <label for="ansager">Ansager:</label>
                <input type="text" class="form-control" id="ansager" name="ansager" value="<?php echo htmlspecialchars($list['ansager']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="schreiber">Schreiber:</label>
                <input type="text" class="form-control" id="schreiber" name="schreiber" value="<?php echo htmlspecialchars($list['schreiber']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="filiale">Filiale:</label>
                <input type="text" class="form-control" id="filiale" name="filiale" value="<?php echo htmlspecialchars($list['filiale']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Speichern</button>
            <a href="<?php echo BASE_URL; ?>views/lists/show.php?liste_id=<?php echo htmlspecialchars($id); ?>" class="btn btn-secondary">Zurück</a>
        </form>
    </div>

    <div class="content bg-white p-4 rounded shadow-sm mt-4">
        <div class="alert alert-success" id="success-message"><?php echo $success_message; ?></div>
        <h2>Fahrzeuge in dieser Liste</h2>
        <?php if ($vehicles->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover ">
                    <thead>
                        <tr>
                            <th>Barcode 8-stellig</th>
                            <th>Abteilung</th>
                            <th>Fgst-Nr</th>
                            <th>Marke</th>
                            <th>Modell</th>
                            <th>Farbe</th>
                            <th>Aufnahme-bereich</th>
                            <th>Bild ersetzen</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($vehicle = $vehicles->fetch_assoc()): ?>
                            <tr>
                                <form class="vehicle-form" method="post" enctype="multipart/form-data">
                                    <td><input type="text" class="form-control" name="barcode8" value="<?php echo htmlspecialchars($vehicle['barcode8']); ?>"></td>
                                    <td>
                                        <select name="abteilung" class="form-control">
                                            <option value="neuwagen" <?php if ($vehicle['abteilung'] == 'neuwagen') echo 'selected'; ?>>Neuwagen</option>
                                            <option value="gebrauchtwagen" <?php if ($vehicle['abteilung'] == 'gebrauchtwagen') echo 'selected'; ?>>Gebrauchtwagen</option>
                                            <option value="großkunden" <?php if ($vehicle['abteilung'] == 'großkunden') echo 'selected'; ?>>Großkunden</option>
                                            <option value="fremdesEigentum" <?php if ($vehicle['abteilung'] == 'fremdesEigentum') echo 'selected'; ?>>Fremdes Eigentum</option>
                                            <option value="bmc" <?php if ($vehicle['abteilung'] == 'bmc') echo 'selected'; ?>>BMW</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="fgNummer" value="<?php echo htmlspecialchars($vehicle['fgNummer']); ?>"></td>
                                    <td><input type="text" class="form-control" name="marke" value="<?php echo htmlspecialchars($vehicle['marke']); ?>"></td>
                                    <td><input type="text" class="form-control" name="modell" value="<?php echo htmlspecialchars($vehicle['modell']); ?>"></td>
                                    <td><input type="text" class="form-control" name="farbe" value="<?php echo htmlspecialchars($vehicle['farbe']); ?>"></td>
                                    <td><input type="text" class="form-control" name="aufnahmebereich" value="<?php echo htmlspecialchars($vehicle['aufnahmebereich']); ?>"></td>
                                    <td>
                                        <input type="file" class="form-control" name="bild" accept="image/*">
                                    </td>
                                    <td>
                                        <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">
                                        <input type="hidden" name="liste_id" value="<?php echo $id; ?>">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-success btn-sm">Speichern</button>
                                            <button type="button" class="btn btn-danger btn-sm delete-vehicle" data-id="<?php echo $vehicle['id']; ?>">Löschen</button>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="alert alert-warning">Keine Fahrzeuge in dieser Liste gefunden.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Bearbeitung der Fahrzeugdaten -->
 <script>
document.querySelectorAll('.vehicle-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo BASE_URL; ?>controllers/VehicleController.php?action=update', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const successMessage = document.getElementById('success-message');
                        successMessage.textContent = response.message;
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    } else {
                        alert('Fehler: ' + response.message);
                    }
                } catch (e) {
                    console.error('Ungültige JSON-Antwort:', xhr.responseText);
                }
            }
        };

        xhr.send(formData);
    });
});

// Fahrzeug löschen
document.querySelectorAll('.delete-vehicle').forEach(button => {
    button.addEventListener('click', function () {
        if (confirm('Möchten Sie dieses Fahrzeug wirklich löschen?')) {
            const vehicleId = this.getAttribute('data-id');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo BASE_URL; ?>controllers/VehicleController.php?action=delete', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Fehler: ' + response.message);
                    }
                } else {
                    alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                }
            };
            xhr.send('id=' + vehicleId);
        }
    });
});

</script>
</body>
</html>
