// Importiere die notwendigen Module aus strich.js
import { StrichSDK, BarcodeReader } from "./strich.js";

// Funktion zum Hinzufügen der Barcode-Erkennungsergebnisse zum DOM
export function addResult(codeDetection) {
    const resultElement = document.createElement('span'); // Erstelle ein neues span-Element
    resultElement.innerHTML = codeDetection.data; // Setze den inneren HTML-Inhalt auf die erkannte Barcode-Daten
    document.getElementById('results').appendChild(resultElement); // Füge das span-Element zum results-Div hinzu
}

// Funktion zur Initialisierung des Barcode-Scanners
export function initializeBarcodeReader() {
    let configuration = {
        selector: '#scanner', // Wähle das Element mit der ID 'scanner' aus
        engine: {
            symbologies: [ // Liste der unterstützten Barcode-Typen
                'databar', 'databar-exp', 'code128', 'code39', 'code93', 'i25', 'codabar',
                'ean13', 'ean8', 'upca', 'upce', 'i25', 'qr'
            ],
            numScanlines: 15, // Anzahl der Scanlinien
            minScanlinesNeeded: 2, // Mindestanzahl der benötigten Scanlinien
            duplicateInterval: 2500 // Intervall zur Vermeidung doppelter Erkennungen (in Millisekunden)
        },
        locator: {
            regionOfInterest: { // Bereich von Interesse für die Erkennung
                left: 0.05, right: 0.05, top: 0.3, bottom: 0.3
            }
        },
        frameSource: {
            resolution: 'full-hd' // Auflösung der Kamera
        },
        overlay: {
            showCameraSelector: true, // Kameraauswahl anzeigen
            showFlashlight: true, // Taschenlampe anzeigen
            showDetections: false // Erkennungen anzeigen
        },
        feedback: {
            audio: true, // Audio-Feedback aktivieren
            vibration: true // Vibrations-Feedback aktivieren
        }
    };
    
    // Initialisiere den Barcode-Reader mit der Konfiguration
    new BarcodeReader(configuration).initialize()
        .then(barcodeReader => {
            window['barcodeReader'] = barcodeReader; // Speichere den Barcode-Reader in der globalen Variable
            // Setze die Erkennungsfunktion
            barcodeReader.detected = (detections) => {
                const barcodeData = detections[0].data; // Hole die Daten des ersten erkannten Barcodes
                addResult(detections[0]); // Füge das Erkennungsergebnis dem DOM hinzu
                fetchVehicleDetailsByAnyBarcode(barcodeData); // Rufe die Fahrzeugdetails anhand des Barcodes ab
            };
            // Starte den Barcode-Reader
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

// Funktion zum Abrufen der Fahrzeugdetails anhand eines Eingabefelds und eines Abfragetypen
export function fetchVehicleDetails(inputField, queryType) {
    const queryValue = inputField.value; // Hole den Wert des Eingabefelds
    const abteilungValue = document.getElementById('abteilung').value; // Speichere die aktuelle Abteilung

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

// Funktion zum Abrufen der Fahrzeugdetails anhand eines Barcodes
export function fetchVehicleDetailsByAnyBarcode(barcode) {
    const urls = [
        `../../controllers/VehicleController.php?action=getVehicleDetails&barcode=${barcode}`,
        `../../controllers/VehicleController.php?action=getVehicleDetails&barcode8=${barcode}`,
        `../../controllers/VehicleController.php?action=getVehicleDetails&fgNummer=${barcode}`
    ];

    // Verwende Promise.any, um die erste erfolgreiche Antwort zu erhalten
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
