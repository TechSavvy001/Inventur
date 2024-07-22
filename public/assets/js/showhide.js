// Importiere BarcodeReader und StrichSDK aus dem 'strich.js' Modul
import { BarcodeReader, StrichSDK } from './strich.js';

// Konfiguration für den Barcode-Reader
let configuration = {
    selector: '#scanner', // Das HTML-Element, das den Scanner hosten wird
    engine: {
        symbologies: [
            'databar', 'databar-exp', 'code128', 'code39', 'code93', 'i25', 'codabar',
            'ean13', 'ean8', 'upca', 'upce', 'i25', 'qr'
        ] // Die unterstützten Barcode-Typen
    },
    locator: {
        regionOfInterest: {
            left: 0.05, right: 0.05, top: 0.3, bottom: 0.3
        } // Das Bereich des Videos, der nach Barcodes durchsucht wird
    },
    frameSource: {
        resolution: 'full-hd' // Die Videoauflösung
    },
    overlay: {
        showCameraSelector: true, // Zeigt den Kamerawähler an
        showFlashlight: true, // Zeigt die Taschenlampenoption an
        showDetections: false // Zeigt erkannte Barcodes nicht an
    },
    feedback: {
        audio: true, // Gibt ein akustisches Signal bei erfolgreicher Erkennung
        vibration: true // Gibt ein Vibrationsfeedback bei erfolgreicher Erkennung
    }
};

let barcodeReader = null; // Variable zum Speichern der BarcodeReader-Instanz
let prompt = document.getElementById('prompt'); // HTML-Element zum Anzeigen von Nachrichten
let hostElement = document.getElementById('scanner'); // HTML-Element zum Hosten des Scanners
hostElement.style.display = 'none'; // Versteckt das Scanner-Element standardmäßig

// Funktion zum Stoppen des Barcode-Scannens
function stopScanning(value) {
    if (barcodeReader) {
        barcodeReader.stop().then(() => {
            barcodeReader.destroy(); // Zerstört die BarcodeReader-Instanz
            barcodeReader = null; // Setzt die Variable zurück
            hostElement.style.display = 'none'; // Versteckt das Scanner-Element
            // Zeigt eine Nachricht mit dem gescannten Barcode an, falls vorhanden
            if (value) {
                prompt.innerText = 'Scanned barcode: ' + value;
            } else {
                prompt.innerText = 'Stopped scanning before an item was scanned';
            }
        }).catch(err => {
            console.error('Error stopping the BarcodeReader:', err);
        });
    } else {
        hostElement.style.display = 'none';
        prompt.innerText = 'Stopped scanning before an item was scanned';
    }
}

// Zeigt eine Nachricht an, dass das SDK initialisiert wird
prompt.innerText = 'Initializing SDK...';

// Lizenzschlüssel für das StrichSDK
const licenseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhNDJlMmMxYy02YjE4LTRhMTYtOTRmZi1mOTU5NjFkOWFkMGEiLCJpc3MiOiJzdHJpY2guaW8iLCJhdWQiOlsiaHR0cHM6Ly9ibXctcmhlaW4tZWR2LmRlIl0sImlhdCI6MTY4ODM2Nzk2NCwibmJmIjoxNjg4MzY3OTY0LCJjYXBhYmlsaXRpZXMiOnsib2ZmbGluZSI6ZmFsc2UsImFuYWx5dGljc09wdE91dCI6ZmFsc2UsImN1c3RvbU92ZXJsYXlMb2dvIjpmYWxzZX0sInZlcnNpb24iOjF9.6b7F7NqxDe4LkNEGD3RzFYkHlD92cvoUYbTfYzOlN78';

// Initialisiert das StrichSDK mit dem Lizenzschlüssel
StrichSDK.initialize(licenseKey)
    .then(() => {
        prompt.innerText = 'SDK initialized successfully, ready to scan.';
    })
    .catch(err => {
        prompt.innerText = 'SDK initialization failed: ' + err.message;
    });

// Funktion zum Starten des Barcode-Scannens
function startScanning(callback) {
    hostElement.style.display = 'block'; // Zeigt das Scanner-Element an
    prompt.innerText = 'Initializing BarcodeReader...';

    // Initialisiert und startet den BarcodeReader
    return new BarcodeReader(configuration).initialize()
        .then(br => {
            barcodeReader = br;
            br.detected = (detections) => {
                stopScanning(detections[0].data); // Stoppt den Scanner bei einer erfolgreichen Erkennung
                if (callback) {
                    callback(detections[0].data); // Ruft den Callback mit den gescannten Daten auf
                }
            };
            return br.start();
        })
        .then(() => {
            prompt.innerText = 'BarcodeReader started successfully.';
        })
        .catch(err => {
            prompt.innerText = 'BarcodeReader initialization failed: ' + err.message;
        });
}

// Exportiere die Funktionen zum Starten und Stoppen des Scannens
export { startScanning, stopScanning };
