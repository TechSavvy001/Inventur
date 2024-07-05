<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fahrzeuge bearbeiten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .alert-success {
            display: none;
        }

        .table-responsive {
            max-width: 100%;
        }

        table {
            width: 100%;
        }

        .form-control {
            min-width: 150px;
        }

        .container {
            max-width: 1200px;
        }

        .content {
            padding: 20px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .btn {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <?php
    include 'config.php';

    // Listen ID abrufen
    $liste_id = $_GET['liste_id'] ?? null;

    if (!$liste_id) {
        echo "<div class='alert alert-danger'>Keine Listen-ID gefunden.</div>";
        exit;
    }

    // SQL-Abfrage zum Abrufen aller Fahrzeuge der aktuellen Liste
    $sql = "SELECT * FROM fahrzeuge WHERE liste_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $liste_id);
    $stmt->execute();
    $vehicles = $stmt->get_result();

    // Verbindung schließen
    $conn->close();
    ?>

    <div class="menubar bg-white shadow-sm py-2 px-4">
        <h1 class="h4">Fahrzeuge bearbeiten</h1>
    </div>

    <div class="container mt-5">
        <div class="content bg-white p-4 rounded shadow-sm">
            <div class="alert alert-success" id="success-message">Änderungen gespeichert!</div>
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
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vehicle = $vehicles->fetch_assoc()): ?>
                                <tr>
                                    <form class="vehicle-form">
                                        <td><input type="text" class="form-control" name="barcode"
                                                value="<?php echo htmlspecialchars($vehicle['barcode']); ?>"></td>
                                        <td><input type="text" class="form-control" name="barcode8"
                                                value="<?php echo htmlspecialchars($vehicle['barcode8']); ?>"></td>
                                        <td>
                                            <select class="form-control" name="abteilung">
                                                <option value="neuwagen" <?php echo $vehicle['abteilung'] == 'neuwagen' ? 'selected' : ''; ?>>Neuwagen</option>
                                                <option value="gebrauchtwagen" <?php echo $vehicle['abteilung'] == 'gebrauchtwagen' ? 'selected' : ''; ?>>Gebrauchtwagen</option>
                                                <option value="großkunden" <?php echo $vehicle['abteilung'] == 'großkunden' ? 'selected' : ''; ?>>Großkunden</option>
                                                <option value="fremdesEigentum" <?php echo $vehicle['abteilung'] == 'fremdesEigentum' ? 'selected' : ''; ?>>Fremdes Eigentum</option>
                                                <option value="bmc" <?php echo $vehicle['abteilung'] == 'bmc' ? 'selected' : ''; ?>>BMW</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="fgNummer"
                                                value="<?php echo htmlspecialchars($vehicle['fgNummer']); ?>"></td>
                                        <td><input type="text" class="form-control" name="marke"
                                                value="<?php echo htmlspecialchars($vehicle['marke']); ?>"></td>
                                        <td><input type="text" class="form-control" name="modell"
                                                value="<?php echo htmlspecialchars($vehicle['modell']); ?>"></td>
                                        <td><input type="text" class="form-control" name="farbe"
                                                value="<?php echo htmlspecialchars($vehicle['farbe']); ?>"></td>
                                        <td><input type="text" class="form-control" name="aufnahmebereich"
                                                value="<?php echo htmlspecialchars($vehicle['aufnahmebereich']); ?>"></td>
                                        <td><input type="text" class="form-control" name="bildNummer"
                                                value="<?php echo htmlspecialchars($vehicle['bildNummer']); ?>"></td>
                                        <td>
                                            <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">
                                            <input type="hidden" name="liste_id" value="<?php echo $liste_id; ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Speichern</button>
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
            <div class="mt-4">
                <a href="aufnahmelisten.php?liste_id=<?php echo $liste_id; ?>" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('.vehicle-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_vehicle.php', true);

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const successMessage = document.getElementById('success-message');
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    }
                };

                xhr.send(formData);
            });
        });
    </script>
</body>

</html>
