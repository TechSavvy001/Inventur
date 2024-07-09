<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fahrzeug erfassen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>

<body>
<div class="container mt-5">
<nav class="menubar bg-white shadow-sm py-2 px-4">
    <div class="container-fluid">
        <h1>Inventur-Aufnahmelisten</h1>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
            <h2>Fahrzeug erfassen</h2>
        </div>

        <div class="content bg-white p-4 rounded shadow-sm">
            <!-- Debugging: Überprüfen der liste_id -->
            <?php if (isset($_GET['liste_id'])): ?>
                <p>Listen-ID: <?php echo htmlspecialchars($_GET['liste_id']); ?></p>
            <?php else: ?>
                <p class="alert alert-danger">Keine Liste ID gefunden.</p>
            <?php endif; ?>

            <form action="../controllers/submit_vehicle.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="liste_id" value="<?php echo htmlspecialchars($_GET['liste_id']); ?>">
                <div class="mb-3">
                    <label for="barcode" class="form-label"><b>Barcode:</b></label>
                    <input type="text" class="form-control" name="barcode" id="barcode" required>
                </div>

                <div class="mb-3">
                    <label for="barcode8" class="form-label"><b>Barcode 8-stellig:</b></label>
                    <input type="text" class="form-control" name="barcode8" id="barcode8" required>
                </div>

                <div class="mb-3">
                    <label for="abteilung" class="form-label"><b>Abteilung</b></label>
                    <select class="form-select" name="abteilung" id="abteilung">
                        <option value="neuwagen">Neuwagen</option>
                        <option value="gebrauchtwagen">Gebrauchtwagen</option>
                        <option value="großkunden">Großkunden</option>
                        <option value="fremdesEigentum">Fremdes Eigentum</option>
                        <option value="bmc">BMW</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fgNummer" class="form-label"><b>Fahrgestellnummer (7-stellig):</b></label>
                    <input type="text" class="form-control" id="fgNummer" name="fgNummer" pattern=".{7}" title="7 Zeichen erforderlich" required>
                </div>

                <div class="mb-3">
                    <label for="marke" class="form-label"><b>Marke:</b></label>
                    <input type="text" class="form-control" id="marke" name="marke" required>
                </div>

                <div class="mb-3">
                    <label for="modell" class="form-label"><b>Modell:</b></label>
                    <input type="text" class="form-control" id="modell" name="modell" required>
                </div>

                <div class="mb-3">
                    <label for="farbe" class="form-label"><b>Farbe:</b></label>
                    <input type="text" class="form-control" id="farbe" name="farbe" required>
                </div>

                <div class="mb-3">
                    <label for="aufnahmebereich" class="form-label"><b>Aufnahmebereich:</b></label>
                    <input type="text" class="form-control" id="aufnahmebereich" name="aufnahmebereich">
                </div>

                <div class="mb-3">
                    <label for="bild" class="form-label"><b>Bild:</b></label><br>
                    <button type="button" class="btn btn-primary" id="openCamera">Kamera öffnen</button><br><br>
                    <input type="file" class="form-control" id="bild" name="bild" accept="image/*" capture="camera" required>
                </div>
                <br><br>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <button type="submit" name="action" value="save_new" class="btn btn-primary btn-lg mb-3">Speichern und Neues Fahrzeug</button>
                    <button type="submit" name="action" value="save_close" class="btn btn-secondary btn-lg mb-3">Speichern und Beenden</button>
                    <button type="button" class="btn btn-danger btn-lg mb-3" onclick="window.location.href='aufnahmelisten.php?liste_id=<?php echo htmlspecialchars($_GET['liste_id']); ?>'">Abbruch</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('openCamera').addEventListener('click', function() {
    // JavaScript Code von Maya zum Öffnen der Kamera
});
</script>
</body>

</html>
