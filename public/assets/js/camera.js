// Variable zum Speichern des Kamera-Streams
let cameraStream;

// Funktion zum Starten der Kamera
function startCamera() {
    const video = document.getElementById('video'); // Das Video-Element, in dem der Kamerastream angezeigt wird
    const canvas = document.getElementById('canvas'); // Das Canvas-Element zum Aufnehmen des Bildes
    const context = canvas.getContext('2d'); // Der 2D-Kontext des Canvas zum Zeichnen des Bildes

    // Zugriff auf die Kamera des Geräts
    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            cameraStream = stream; // Den Kamera-Stream in der globalen Variablen speichern
            video.srcObject = stream; // Den Stream dem Video-Element zuweisen
            video.play(); // Das Video abspielen
        })
        .catch((err) => {
            console.error('Error accessing the camera: ', err); // Fehlerbehandlung bei Zugriff auf die Kamera
        });
}

// Funktion zum Stoppen der Kamera
function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop()); // Alle Tracks des Kamera-Streams stoppen
        cameraStream = null; // Die Kamera-Stream-Variable zurücksetzen
    }
    const video = document.getElementById('video'); // Das Video-Element
    video.srcObject = null; // Den Stream des Video-Elements entfernen
}

// Funktion zum Aufnehmen des Bildes
function captureImage() {
    return new Promise((resolve, reject) => {
        try {
            const video = document.getElementById('video'); // Das Video-Element
            const canvas = document.getElementById('canvas'); // Das Canvas-Element
            const context = canvas.getContext('2d'); // Der 2D-Kontext des Canvas

            canvas.width = video.videoWidth; // Setzen der Canvas-Breite auf die Video-Breite
            canvas.height = video.videoHeight; // Setzen der Canvas-Höhe auf die Video-Höhe
            context.drawImage(video, 0, 0, canvas.width, canvas.height); // Zeichnen des aktuellen Videobildes auf das Canvas

            const imageUrl = canvas.toDataURL('image/png'); // Erstellen einer Data-URL des Bildes im PNG-Format
            resolve(imageUrl); // Auflösung der Data-URL

            stopCamera(); // Die Kamera nach dem Aufnehmen des Bildes stoppen
        } catch (error) {
            reject('Fehler beim Aufnehmen des Bildes: ' + error);
        }
    });
}

// Exportieren der Funktionen für den Import in anderen Dateien
export { startCamera, stopCamera, captureImage };
