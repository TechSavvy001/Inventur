import { StrichSDK, BarcodeReader } from "./strich.js";

/**
 * Erkannten Code zum DOM hinzufügen
 */
function addResult(codeDetection) {
    const resultElement = document.createElement('span');
    resultElement.innerHTML = codeDetection.data;
    document.getElementById('results').appendChild(resultElement);
}

/**
 * STRICH BarcodeReader initialisieren und starten.
 */
function initializeBarcodeReader() {
    let configuration = {
        selector: '#scanner', // Verwenden Sie eine eindeutige ID
        engine: {
            // Alle 1D-Symbologien
            symbologies: [
                'databar', 'databar-exp', 'code128', 'code39', 'code93', 'i25', 'codabar',
                'ean13', 'ean8', 'upca', 'upce', 'i25'
            ],
            numScanlines: 15,
            minScanlinesNeeded: 2,
            duplicateInterval: 2500
        },
        locator: {
            regionOfInterest: {
                left: 0.05, right: 0.05, top: 0.3, bottom: 0.3 // Schmaler Bereich für 1D
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
            // BarcodeReader in einer globalen Variablen speichern, um später darauf zugreifen zu können (z.B. zum Zerstören)
            window['barcodeReader'] = barcodeReader;
            barcodeReader.detected = (detections) => {
                addResult(detections[0]);
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

/**
 * STRICH SDK initialisieren und bei Erfolg den BarcodeReader initialisieren.
 */
StrichSDK.initialize('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhNDJlMmMxYy02YjE4LTRhMTYtOTRmZi1mOTU5NjFkOWFkMGEiLCJpc3MiOiJzdHJpY2guaW8iLCJhdWQiOlsiaHR0cHM6Ly9ibXctcmhlaW4tZWR2LmRlIl0sImlhdCI6MTY4ODM2Nzk2NCwibmJmIjoxNjg4MzY3OTY0LCJjYXBhYmlsaXRpZXMiOnsib2ZmbGluZSI6ZmFsc2UsImFuYWx5dGljc09wdE91dCI6ZmFsc2UsImN1c3RvbU92ZXJsYXlMb2dvIjpmYWxzZX0sInZlcnNpb24iOjF9.6b7F7NqxDe4LkNEGD3RzFYkHlD92cvoUYbTfYzOlN78')
    .then(() => {
        initializeBarcodeReader();
    })
    .catch(err => {
        window.alert('SDK konnte nicht initialisiert werden: ' + err);
    });
