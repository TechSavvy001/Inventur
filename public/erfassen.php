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
    <style>
        #scanner {
            position: relative; /* Fügt die erforderliche CSS-Regel hinzu */
            width: 100%; /* Sicherstellen, dass das Element eine sichtbare Breite hat */
            height: 300px; /* Sicherstellen, dass das Element eine sichtbare Höhe hat */
        }
    </style>
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

                <form action="../controllers/submit_vehicle.php" method="post" enctype="multipart/form-data" id="vehicleForm">
                    <input type="hidden" name="liste_id" value="<?php echo htmlspecialchars($_GET['liste_id']); ?>">
                    <input type="hidden" id="bildData" name="bildData">
                    <div class="mb-3">
                        <label for="barcode" class="form-label"><b>Barcode:</b></label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="barcode" id="barcode" required pattern="[A-Za-z0-9]{6,12}" title="6 bis 12 alphanumerische Zeichen">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scannerModal">Barcode scannen</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="barcode8" class="form-label"><b>Barcode 8-stellig:</b></label>
                        <input type="text" class="form-control" name="barcode8" id="barcode8" required pattern="[A-Za-z0-9]{8}" title="8 alphanumerische Zeichen">
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
                        <input type="text" class="form-control" id="fgNummer" name="fgNummer" pattern="[A-Za-z0-9]{7}" title="7 alphanumerische Zeichen" required>
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
                        <button type="button" class="btn btn-primary" id="openCamera">Bild machen</button><br><br>
                        <input type="file" class="form-control" id="bild" name="bild" accept="image/*" capture="camera">
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

<!-- Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel">Barcode Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="scanner" style="height: 300px; width: 100%; background: #000;"></div> <!-- Stellen Sie sicher, dass das Element die ID "scanner" hat -->
                <div id="prompt" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- STRICH SDK und benutzerdefiniertes Skript hinzufügen -->
<script type="module">
    import { startScanning, stopScanning } from './showhide.js';

    document.getElementById('scannerModal').addEventListener('shown.bs.modal', function() {
        document.getElementById('scanner').style.display = 'block';
        startScanning((barcode) => {
            document.getElementById('barcode').value = barcode;

            fetch(`../controllers/fetch_vehicle.php?barcode=${barcode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('fgNummer').value = data.fgNummer;
                        document.getElementById('marke').value = data.marke;
                        document.getElementById('modell').value = data.modell;
                        document.getElementById('farbe').value = data.farbe;
                        document.getElementById('abteilung').value = data.abteilung;
                    }
                });
        });
    });

    document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function() {
        stopScanning(null);
        document.getElementById('scanner').style.display = 'none';
    });

    document.getElementById('openCamera').addEventListener('click', function() {
        const video = document.createElement('video');
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const captureButton = document.createElement('button');
        captureButton.textContent = 'Capture';

        document.body.appendChild(video);
        document.body.appendChild(captureButton);

        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                video.srcObject = stream;
                video.play();
            })
            .catch((err) => {
                console.error('Error accessing the camera: ', err);
            });

        captureButton.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageUrl = canvas.toDataURL('image/png');
            document.getElementById('bildData').value = imageUrl;
            
            // Stop all video streams.
            video.srcObject.getTracks().forEach(track => track.stop());
            
            // Remove video and button elements from the DOM
            document.body.removeChild(video);
            document.body.removeChild(captureButton);
        });
    });

    function fetchVehicleDetails(inputField, queryType) {
        const queryValue = inputField.value;
        if (queryValue) {
            fetch(`../controllers/fetch_vehicle.php?${queryType}=${queryValue}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('barcode').value = data.barcode;
                        document.getElementById('barcode8').value = data.barcode8;
                        document.getElementById('fgNummer').value = data.fgNummer;
                        document.getElementById('marke').value = data.marke;
                        document.getElementById('modell').value = data.modell;
                        document.getElementById('farbe').value = data.farbe;
                        document.getElementById('abteilung').value = data.abteilung;
                    }
                });
        }
    }

    document.getElementById('barcode').addEventListener('focusout', function() {
        fetchVehicleDetails(this, 'barcode');
    });

    document.getElementById('fgNummer').addEventListener('focusout', function() {
        fetchVehicleDetails(this, 'fgNummer');
    });

    document.getElementById('vehicleForm').addEventListener('submit', function(event) {
        var barcode = document.getElementById('barcode').value;
        var barcode8 = document.getElementById('barcode8').value;
        var fgNummer = document.getElementById('fgNummer').value;

        if (!/^[A-Za-z0-9]{6,12}$/.test(barcode)) {
            alert('Der Barcode muss 6 bis 12 alphanumerische Zeichen enthalten.');
            event.preventDefault();
        }

        if (!/^[A-Za-z0-9]{8}$/.test(barcode8)) {
            alert('Der Barcode8 muss 8 alphanumerische Zeichen enthalten.');
            event.preventDefault();
        }

        if (!/^[A-Za-z0-9]{7}$/.test(fgNummer)) {
            alert('Die Fahrgestellnummer muss 7 alphanumerische Zeichen enthalten.');
            event.preventDefault();
        }
    });
</script>
<script type="module" src="showhide.js"></script>
<script type="module" src="main.js"></script>
<script type="module" src="camera.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
