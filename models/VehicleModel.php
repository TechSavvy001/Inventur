<?php
class VehicleModel {
    private $conn;
    private $error;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Methode zum Abrufen eines Fahrzeugs anhand des Barcodes aus der Tabelle 'bestandsfahrzeuge'
    public function getVehicleByBarcode($barcode) {
        $stmt = $this->conn->prepare("SELECT * FROM bestandsfahrzeuge WHERE barcode = ?");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // Methode zum Abrufen eines Fahrzeugs anhand der Fahrgestellnummer aus der Tabelle 'bestandsfahrzeuge'
    public function getVehicleByFgNummer($fgNummer) {
        $stmt = $this->conn->prepare("SELECT * FROM bestandsfahrzeuge WHERE fgNummer = ?");
        $stmt->bind_param("s", $fgNummer);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Methode zum Abrufen eines Fahrzeugs anhand des 8-stelligen Barcodes aus der Tabelle 'bestandsfahrzeuge'
    public function getVehicleByBarcode8($barcode8) {
        $stmt = $this->conn->prepare("SELECT * FROM bestandsfahrzeuge WHERE barcode8 = ?");
        $stmt->bind_param("s", $barcode8);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Neue Methode zum Abrufen eines Fahrzeugs anhand der Fahrgestellnummer aus der Tabelle 'fahrzeuge'
    public function getVehicleByFgNummerFromFahrzeuge($fgNummer) {
        $stmt = $this->conn->prepare("SELECT * FROM fahrzeuge WHERE fgNummer = ?");
        $stmt->bind_param("s", $fgNummer);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // Methode zum Einfügen eines neuen Fahrzeugs in die Tabelle 'fahrzeuge'
    public function insertVehicleIntoFahrzeuge($vehicle) {
        $stmt = $this->conn->prepare("INSERT INTO fahrzeuge (barcode, barcode8, abteilung, fgNummer, marke, modell, farbe, aufnahmebereich, liste_id, bildPfad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $vehicle['barcode'], $vehicle['barcode8'], $vehicle['abteilung'], $vehicle['fgNummer'], $vehicle['marke'], $vehicle['modell'], $vehicle['farbe'], $vehicle['aufnahmebereich'], $vehicle['liste_id'], $vehicle['bildPfad']);
        
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error = $stmt->error;
            return false;
        }
    }

    // Methode zum Erstellen eines neuen Fahrzeugs in der Tabelle 'fahrzeuge'
    public function createVehicle($data) {
        $stmt = $this->conn->prepare("INSERT INTO fahrzeuge (barcode, barcode8, abteilung, fgNummer, marke, modell, farbe, aufnahmebereich, bildNummer, liste_id, bildPfad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $data['barcode'], $data['barcode8'], $data['abteilung'], $data['fgNummer'], $data['marke'], $data['modell'], $data['farbe'], $data['aufnahmebereich'], $data['bildNummer'], $data['liste_id'], $data['bildPfad']);
        
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error = $stmt->error;
            return false;
        }
    }

    // Methode zum Aktualisieren eines Fahrzeugs in der Tabelle 'fahrzeuge'
    public function updateVehicle($data) {
        $stmt = $this->conn->prepare("UPDATE fahrzeuge SET barcode = ?, barcode8 = ?, abteilung = ?, fgNummer = ?, marke = ?, modell = ?, farbe = ?, aufnahmebereich = ?, bildPfad = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $data['barcode'], $data['barcode8'], $data['abteilung'], $data['fgNummer'], $data['marke'], $data['modell'], $data['farbe'], $data['aufnahmebereich'], $data['bildPfad'], $data['id']);
        
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error = $stmt->error;
            return false;
        }
    }

    // Methode zum Löschen eines Fahrzeugs in der Tabelle 'fahrzeuge'
    public function deleteVehicle($id) {
        $stmt = $this->conn->prepare("DELETE FROM fahrzeuge WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error = $stmt->error;
            return false;
        }
    }

    // Methode zum Abrufen eines Fahrzeugs anhand der ID aus der Tabelle 'fahrzeuge'
    public function getVehicleById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Methode zum Abrufen aller Fahrzeuge anhand der Listen-ID aus der Tabelle 'fahrzeuge'
    public function getByListId($liste_id) {
        $stmt = $this->conn->prepare("SELECT * FROM fahrzeuge WHERE liste_id = ?");
        $stmt->bind_param("i", $liste_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getError() {
        return $this->error;
    }
}
