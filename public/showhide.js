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
    barcodeReader.stop();
    barcodeReader.destroy();
    barcodeReader = null;
    hostElement.style.display = 'none';
    if (value) {
        prompt.innerText = 'Scanned barcode: ' + value;
    } else {
        prompt.innerText = 'Stopped scanning before an item was scanned';
    }
}

StrichSDK.initialize('<your-license-key>')
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
        });
}

export { startScanning, stopScanning };
