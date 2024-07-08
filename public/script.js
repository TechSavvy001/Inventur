document.getElementById('vehicleForm').onsubmit = function() {
    if (!document.getElementById('barcode').value || !document.getElementById('fahrgestellnummer').value || !document.getElementById('modell').value) {
        alert("Bitte füllen Sie alle Felder aus.");
        return false;
    }
    return true;
};
