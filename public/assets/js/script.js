// Füge ein Event-Listener für das Formular hinzu, um die Eingaben zu überprüfen, bevor es abgeschickt wird
document.getElementById('vehicleForm').onsubmit = function() {
    // Überprüfe, ob die erforderlichen Felder (Barcode, Fahrgestellnummer und Modell) ausgefüllt sind
    if (!document.getElementById('barcode').value || !document.getElementById('fgNummer').value || !document.getElementById('modell').value) {
        // Wenn eines der Felder leer ist, zeige eine Warnung und verhindere das Abschicken des Formulars
        alert("Bitte füllen Sie alle Felder aus.");
        return false; // Verhindere das Abschicken des Formulars
    }
    return true; // Erlaube das Abschicken des Formulars
};
