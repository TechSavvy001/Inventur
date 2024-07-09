<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste bearbeiten</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css.css">
    <style>
        .alert-success {
            display: none;
        }
    </style>
</head>

<body>
<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: ../public/login.php');
        exit;
    }

    include '../config/config.php';

    $id = $_GET['id'];

    $sql = "SELECT * FROM listen WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = $result->fetch_assoc();

    $sql = "SELECT * FROM fahrzeuge WHERE liste_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $vehicles = $stmt->get_result();

    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../public/listen_bearbeiten.php';
    ?>

    <div class="container mt-5">
        <div class="menubar bg-white shadow-sm py-2 px-4">
            <h1>Liste bearbeiten</h1>
        </div>

        <div class="content bg-white p-4 rounded shadow-sm mt-4">
            <form action="update_list.php" method="post">
                <input type="hidden" name="id" value="<?php echo $list['id']; ?>">
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
                <a href="<?php echo $previous_page; ?>" class="btn btn-secondary">Zurück</a>
            </form>
        </div>

        <div class="content bg-white p-4 rounded shadow-sm mt-4">
            <div class="alert alert-success" id="success-message">Änderungen gespeichert!</div>
            <h2>Fahrzeuge in dieser Liste</h2>
            <?php if ($vehicles->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Barcode 8-stellig</th>
                                <th>Abteilung</th>
                                <th>Fgst-Nr</th>
                                <th>Marke</th>
                                <th>Modell</th>
                                <th>Farbe</th>
                                <th>Aufnahmebereich</th>
                                <th>Bild ersetzen</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vehicle = $vehicles->fetch_assoc()): ?>
                                <tr>
                                    <form class="vehicle-form" method="post" enctype="multipart/form-data">
                                        <td><input type="text" class="form-control" name="barcode" value="<?php echo htmlspecialchars($vehicle['barcode']); ?>"></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>

<!-- Bearbeitung der Fahrzeudaten -->
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

    // Fahrzeug löschen
    document.querySelectorAll('.delete-vehicle').forEach(button => {
        button.addEventListener('click', function () {
            if (confirm('Möchten Sie dieses Fahrzeug wirklich löschen?')) {
                const vehicleId = this.getAttribute('data-id');
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_vehicle.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send('id=' + vehicleId);
            }
        });
    });
</script>
</body>

</html>