<?php
// Startet die Session
session_start();

// Setzt den Seitentitel auf "Fahrzeug erfassen"
$title = "Fahrzeug erfassen";

// Einbinden des gemeinsamen Headers
include '../layouts/header.php';

// Bindet die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once '../../config/config.php';

// Bindet den ListController ein, um Listenaktionen zu verwalten
include_once '../../controllers/ListController.php';

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Wenn nicht, wird der Benutzer zur Login-Seite weitergeleitet
    header('Location: ../../views/users/login.php');
    exit;
}

// Überprüfen, ob die Listen-ID in der URL übergeben wurde
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
    <title>Fahrzeug erfassen</title>
    <link rel="stylesheet" href="../../public/assets/css/css.css">
    <style>
        #scanner, #video {
            position: relative;
            width: 100%;
            height: 500px; /* Erhöht die Höhe des Scanner- und Videoelements */
        }
        #canvas {
            display: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
            <h1>Fahrzeug erfassen</h1>
        </div>

        <div class="content bg-white p-4 rounded shadow-sm">

            <form action="../../controllers/VehicleController.php?action=store" method="post" enctype="multipart/form-data" id="vehicleForm">
                <input type="hidden" name="liste_id" value="<?php echo htmlspecialchars($_GET['liste_id']); ?>">
                <input type="hidden" id="bildData" name="bildData">

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scannerModal">Barcode scannen</button><br><br>

                <div class="mb-3">
                    <label for="barcode" class="form-label"><b>Barcode:</b></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="barcode" id="barcode">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="barcode8" class="form-label"><b>Barcode 8-stellig:</b></label>
                    <input type="text" class="form-control" name="barcode8" id="barcode8" pattern="[A-Za-z0-9]{8}" title="8 alphanumerische Zeichen">
                </div>

                <div class="mb-3">
                    <label for="abteilung" class="form-label"><b>Abteilung</b></label>
                    <select class="form-select" name="abteilung" id="abteilung">
                        <option value="neuwagen" <?php echo isset($_POST['abteilung']) && $_POST['abteilung'] == 'neuwagen' ? 'selected' : ''; ?>>Neuwagen</option>
                        <option value="gebrauchtwagen" <?php echo isset($_POST['abteilung']) && $_POST['abteilung'] == 'gebrauchtwagen' ? 'selected' : ''; ?>>Gebrauchtwagen</option>
                        <option value="großkunden" <?php echo isset($_POST['abteilung']) && $_POST['abteilung'] == 'großkunden' ? 'selected' : ''; ?>>Großkunden</option>
                        <option value="fremdesEigentum" <?php echo isset($_POST['abteilung']) && $_POST['abteilung'] == 'fremdesEigentum' ? 'selected' : ''; ?>>Fremdes Eigentum</option>
                        <option value="bmc" <?php echo isset($_POST['abteilung']) && $_POST['abteilung'] == 'bmc' ? 'selected' : ''; ?>>BMW</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fgNummer" class="form-label"><b>Fahrgestellnummer (7-stellig):</b></label>
                    <input type="text" class="form-control" id="fgNummer" name="fgNummer" pattern="[A-Za-z0-9]{7}" title="7 alphanumerische Zeichen">
                </div>

                <div class="mb-3">
                    <label for="marke" class="form-label"><b>Marke:</b></label>
                    <input type="text" class="form-control" id="marke" name="marke" value="<?php echo isset($_POST['marke']) ? htmlspecialchars($_POST['marke']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="modell" class="form-label"><b>Modell:</b></label>
                    <input type="text" class="form-control" id="modell" name="modell" value="<?php echo isset($_POST['modell']) ? htmlspecialchars($_POST['modell']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="farbe" class="form-label"><b>Farbe:</b></label>
                    <input type="text" class="form-control" id="farbe" name="farbe" value="<?php echo isset($_POST['farbe']) ? htmlspecialchars($_POST['farbe']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="aufnahmebereich" class="form-label"><b>Aufnahmebereich:</b></label>
                    <input type="text" class="form-control" id="aufnahmebereich" name="aufnahmebereich" value="<?php echo isset($_POST['aufnahmebereich']) ? htmlspecialchars($_POST['aufnahmebereich']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="bild" class="form-label"><b>Bild:</b></label><br>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cameraModal">Bild machen</button><br><br>
                    <input type="file" class="form-control" id="bild" name="bild" accept="image/*" capture="camera">
                </div>
                <br><br>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <button type="submit" name="action" value="save_new" class="btn btn-primary btn-lg mb-3">Speichern und Neues Fahrzeug</button>
                    <button type="submit" name="action" value="save_close" class="btn btn-secondary btn-lg mb-3">Speichern und Beenden</button>
                    <button type="button" class="btn btn-danger btn-lg mb-3" onclick="window.location.href='../lists/show.php?liste_id=<?php echo htmlspecialchars($_GET['liste_id']); ?>'">Abbruch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal für Barcode Scanner -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel">Barcode Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="scanner" style="height: 500px; width: 100%; background: #000;"></div>
                <div id="prompt" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Kamera -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Kamera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="video" style="width: 100%; height: 500px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <button id="captureButton" class="btn btn-primary mt-3">Bild machen</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { StrichSDK, BarcodeReader } from '../../public/assets/js/strich.js';
    import { startScanning, stopScanning } from '../../public/assets/js/showhide.js';
    import { startCamera, stopCamera } from '../../public/assets/js/camera.js'; // Korrekte Import

    let barcodeReader;
    
    document.getElementById('scannerModal').addEventListener('shown.bs.modal', function() {
        document.getElementById('scanner').style.display = 'block';

        if (!barcodeReader) {  // Initialisieren Sie den Barcode-Reader nur einmal
            StrichSDK.initialize('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhNDJlMmMxYy02YjE4LTRhMTYtOTRmZi1mOTU5NjFkOWFkMGEiLCJpc3MiOiJzdHJpY2guaW8iLCJhdWQiOlsiaHR0cHM6Ly9ibXctcmhlaW4tZWR2LmRlIl0sImlhdCI6MTY4ODM2Nzk2NCwibmJmIjoxNjg4MzY3OTY0LCJjYXBhYmlsaXRpZXMiOnsib2ZmbGluZSI6ZmFsc2UsImFuYWx5dGljc09wdE91dCI6ZmFsc2UsImN1c3RvbU92ZXJsYXlMb2dvIjpmYWxzZX0sInZlcnNpb24iOjF9.6b7F7NqxDe4LkNEGD3RzFYkHlD92cvoUYbTfYzOlN78')
                .then(() => {
                    initializeBarcodeReader();
                })
                .catch(err => {
                    window.alert('SDK konnte nicht initialisiert werden: ' + err);
                });
        }
    });

    document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function() {
        if (barcodeReader) {
            barcodeReader.stop().then(() => {
                barcodeReader.destroy();
                barcodeReader = null;
                document.getElementById('scanner').style.display = 'none';
            }).catch(err => {
                console.error('Error stopping the BarcodeReader:', err);
            });
        }
    });

    document.getElementById('cameraModal').addEventListener('shown.bs.modal', function() {
        startCamera();
    });

    document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function() {
        stopCamera();
    });

    function initializeBarcodeReader() {
        let configuration = {
            selector: '#scanner',
            engine: {
                symbologies: [
                    'databar', 'databar-exp', 'code128', 'code39', 'code93', 'i25', 'codabar',
                    'ean13', 'ean8', 'upca', 'upce', 'i25', 'qr'
                ],
                numScanlines: 15,
                minScanlinesNeeded: 2,
                duplicateInterval: 2500
            },
            locator: {
                regionOfInterest: {
                    left: 0.05, right: 0.05, top: 0.3, bottom: 0.3
                }
            },
            frameSource: {
                resolution: 'full-hd'
            },
            overlay: {
                showCameraSelector: true,
                showFlashlight: true,
                showDetections: false
            },
            feedback: {
                audio: true,
                vibration: true
            }
        };
        new BarcodeReader(configuration).initialize()
            .then(reader => {
                barcodeReader = reader;
                barcodeReader.detected = (detections) => {
                    const detectedBarcode = detections[0].data;
                    // Versuche zuerst mit barcode8
                    fetchVehicleDetailsByAnyBarcode({ value: detectedBarcode }, 'barcode8');
                };
                barcodeReader.start().then(() => {
                    console.log(`BarcodeReader.start() erfolgreich`);
                }).catch(err => {
                    console.error(`BarcodeReader.start() fehlgeschlagen: ${err}`);
                });
            })
            .catch(error => {
                console.error(`Initialisierungsfehler: ${error}`);
            });
    }

    // Funktion zum Abrufen der Fahrzeugdetails und Ausfüllen des Formulars
    function fillFormFields(inputField, queryType) {
        fetchVehicleDetailsByAnyBarcode(inputField, queryType)
    }

    function fetchVehicleDetailsByAnyBarcode(inputField, queryType) {
        console.log('Fetch details for:', queryType, 'with value:', inputField.value);

        // Speichere die aktuellen Werte der Felder
        const abteilungField = document.getElementById('abteilung');
        const aufnahmebereichField = document.getElementById('aufnahmebereich');
        const currentAbteilungValue = abteilungField.value;
        const currentAufnahmebereichValue = aufnahmebereichField.value;

        const queryValue = inputField.value;
        if (queryValue) {
            fetch(`../../controllers/VehicleController.php?action=getVehicleDetails&${queryType}=${queryValue}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Netzwerkantwort war nicht ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const vehicle = data.vehicle;
                        document.getElementById('barcode').value = vehicle.barcode;
                        document.getElementById('barcode8').value = vehicle.barcode8;
                        document.getElementById('fgNummer').value = vehicle.fgNummer;
                        document.getElementById('marke').value = vehicle.marke;
                        document.getElementById('modell').value = vehicle.modell;
                        document.getElementById('farbe').value = vehicle.farbe;

                        // Aktualisiere die Abteilung nur, wenn sie leer ist
                        if (currentAbteilungValue === "") {
                            abteilungField.value = vehicle.abteilung;
                        }

                        // Aktualisiere den Aufnahmebereich nur, wenn er leer ist
                        if (currentAufnahmebereichValue === "") {
                            aufnahmebereichField.value = vehicle.aufnahmebereich;
                        }
                    } else {
                        if (queryType === 'barcode8') {
                            console.warn('Fahrzeug nicht gefunden mit barcode8. Versuche fgNummer.');
                            fetchVehicleDetailsByAnyBarcode(inputField, 'fgNummer');
                        } else {
                            console.error('Fahrzeug nicht gefunden:', data.message);
                            alert('Fahrzeug nicht gefunden: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Fehler beim Abrufen der Fahrzeugdetails:', error);
                    alert('Fehler beim Abrufen der Fahrzeugdetails.');
                });
        }
    }

    // Event Listener hinzufügen
    document.getElementById('barcode').addEventListener('focusout', function() {
        fetchVehicleDetailsByAnyBarcode(this, 'barcode');
    });

    document.getElementById('barcode8').addEventListener('focusout', function() {
        fetchVehicleDetailsByAnyBarcode(this, 'barcode8');
    });

    document.getElementById('fgNummer').addEventListener('focusout', function() {
        fetchVehicleDetailsByAnyBarcode(this, 'fgNummer');
    });

    document.getElementById('vehicleForm').addEventListener('submit', function(event) {
        var barcode = document.getElementById('barcode').value;
        var barcode8 = document.getElementById('barcode8').value;
        var fgNummer = document.getElementById('fgNummer').value;

        // if (!/^[A-Za-z0-9]{6,12}$/.test(barcode)) {
        //    alert('Der Barcode muss 6 bis 12 alphanumerische Zeichen enthalten.');
        //    event.preventDefault();
        //}

        if (!/^[A-Za-z0-9]{8}$/.test(barcode8)) {
            alert('Der Barcode muss genau 8 Ziffern enthalten.');
            event.preventDefault();
        }

        if (!/^[A-Za-z0-9]{7}$/.test(fgNummer)) {
            alert('Die Fahrgestellnummer muss 7 alphanumerische Zeichen enthalten.');
            event.preventDefault();
        }
    });

    if (document.getElementById('success-message')) {
        setTimeout(() => {
            document.getElementById('success-message').style.display = 'none';
        }, 3000);
    }
</script>

<script type="module" src="../../public/assets/js/showhide.js"></script>
<script type="module" src="../../public/assets/js/main.js"></script>
<script type="module" src="../../public/assets/js/camera.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
