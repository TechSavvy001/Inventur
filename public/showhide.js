import { StrichSDK, BarcodeReader } from "./strich.js";

let configuration = {
    selector: '#scanner',
    engine: {
        symbologies: ['ean13']
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

let barcodeReader = null;
let prompt = document.getElementById('prompt');
let hostElement = document.getElementById('scanner');
hostElement.style.display = 'none';

function stopScanning(value) {
    if (barcodeReader) {
        barcodeReader.stop().then(() => {
            barcodeReader.destroy();
            barcodeReader = null;
            hostElement.style.display = 'none';
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

prompt.innerText = 'Initializing SDK...';

const licenseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhNDJlMmMxYy02YjE4LTRhMTYtOTRmZi1mOTU5NjFkOWFkMGEiLCJpc3MiOiJzdHJpY2guaW8iLCJhdWQiOlsiaHR0cHM6Ly9ibXctcmhlaW4tZWR2LmRlIl0sImlhdCI6MTY4ODM2Nzk2NCwibmJmIjoxNjg4MzY3OTY0LCJjYXBhYmlsaXRpZXMiOnsib2ZmbGluZSI6ZmFsc2UsImFuYWx5dGljc09wdE91dCI6ZmFsc2UsImN1c3RvbU92ZXJsYXlMb2dvIjpmYWxzZX0sInZlcnNpb24iOjF9.6b7F7NqxDe4LkNEGD3RzFYkHlD92cvoUYbTfYzOlN78';

StrichSDK.initialize(licenseKey)
    .then(() => {
        prompt.innerText = 'SDK initialized successfully, ready to scan.';
    })
    .catch(err => {
        prompt.innerText = 'SDK initialization failed: ' + err.message;
    });

function startScanning(callback) {
    hostElement.style.display = 'block';
    prompt.innerText = 'Initializing BarcodeReader...';

    return new BarcodeReader(configuration).initialize()
        .then(br => {
            barcodeReader = br;
            br.detected = (detections) => {
                stopScanning(detections[0].data);
                if (callback) {
                    callback(detections[0].data);
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

export { startScanning, stopScanning };
