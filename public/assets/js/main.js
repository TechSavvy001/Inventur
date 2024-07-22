import { StrichSDK, BarcodeReader } from "./strich.js";


export function addResult(codeDetection) {
    const resultElement = document.createElement('span');
    resultElement.innerHTML = codeDetection.data;
    document.getElementById('results').appendChild(resultElement);
}

export function initializeBarcodeReader() {
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
        .then(barcodeReader => {
            window['barcodeReader'] = barcodeReader;
            barcodeReader.detected = (detections) => {
                const barcodeData = detections[0].data;
                addResult(detections[0]);
                fetchVehicleDetailsByAnyBarcode(barcodeData);
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

export function fetchVehicleDetails(inputField, queryType) {
    const queryValue = inputField.value;
    const abteilungValue = document.getElementById('abteilung').value; // Speichern der aktuellen Abteilung

    if (queryValue) {
        fetch(`../../controllers/VehicleController.php?action=getVehicleDetails&${queryType}=${queryValue}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('barcode').value = data.vehicle.barcode;
                    document.getElementById('barcode8').value = data.vehicle.barcode8;
                    document.getElementById('fgNummer').value = data.vehicle.fgNummer;
                    document.getElementById('marke').value = data.vehicle.marke;
                    document.getElementById('modell').value = data.vehicle.modell;
                    document.getElementById('farbe').value = data.vehicle.farbe;
                    
                    // Abteilung nur aktualisieren, wenn sie leer ist
                    if (abteilungValue === "") {
                        document.getElementById('abteilung').value = data.vehicle.abteilung;
                    }
                }
            });
    }
}

export function fetchVehicleDetailsByAnyBarcode(barcode) {
    const urls = [
        `../../controllers/VehicleController.php?action=getVehicleDetails&barcode=${barcode}`,
        `../../controllers/VehicleController.php?action=getVehicleDetails&barcode8=${barcode}`,
        `../../controllers/VehicleController.php?action=getVehicleDetails&fgNummer=${barcode}`
    ];

    Promise.any(urls.map(url => fetch(url).then(response => response.json())))
        .then(data => {
            if (data.success) {
                const vehicle = data.vehicle;
                document.getElementById('barcode').value = vehicle.barcode;
                document.getElementById('barcode8').value = vehicle.barcode8;
                document.getElementById('fgNummer').value = vehicle.fgNummer;
                document.getElementById('marke').value = vehicle.marke;
                document.getElementById('modell').value = vehicle.modell;
                document.getElementById('farbe').value = vehicle.farbe;
                document.getElementById('abteilung').value = vehicle.abteilung;
            } else {
                console.error('Fahrzeug nicht gefunden.');
            }
        })
        .catch(error => {
            console.error('Fehler beim Abrufen der Fahrzeugdetails:', error);
        });
}
