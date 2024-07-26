<?php
class VehicleModel {
    private $conn;  // Datenbankverbindung
    private $error; // Variable zum Speichern von Fehlern

    // Konstruktor, um die Datenbankverbindung zu initialisieren
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Methode zum Abrufen eines Fahrzeugs anhand des Barcodes aus der Tabelle 'bestandsfahrzeuge'
    public function getVehicleByBarcode($barcode) {
        $stmt = $this->conn->prepare("SELECT * FROM bestandsfahrzeuge WHERE barcode = ?");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Rückgabe des gefundenen Fahrzeugs als assoziatives Array
    }

    // Methode zum Abrufen eines Fahrzeugs anhand der Fahrgestellnummer aus der Tabelle 'bestandsfahrzeuge'
    public function getVehicleByFgNummer($fgNummer) {
        $stmt = $this->conn->prepare("SELECT * FROM bestandsfahrzeuge WHERE fgNummer = ?");
        $stmt->bind_param("s", $fgNummer);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Rückgabe des gefundenen Fahrzeugs als assoziatives Array
    }

    // Methode zum Abrufen eines Fahrzeugs anhand des 8-stelligen Barcodes aus der Tabelle 'bestandsfahrzeuge'
    public function getVehicleByBarcode8($barcode8) {
        $stmt = $this->conn->prepare("SELECT * FROM bestandsfahrzeuge WHERE barcode8 = ?");
        $stmt->bind_param("s", $barcode8);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Rückgabe des gefundenen Fahrzeugs als assoziatives Array
    }

    // Methode zum Abrufen eines Fahrzeugs anhand der Fahrgestellnummer aus der Tabelle 'fahrzeuge'
    public function getVehicleByFgNummerFromFahrzeuge($fgNummer) {
        $stmt = $this->conn->prepare("SELECT * FROM fahrzeuge WHERE fgNummer = ?");
        $stmt->bind_param("s", $fgNummer);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Rückgabe des gefundenen Fahrzeugs als assoziatives Array
    }

    // Methode zum Einfügen eines neuen Fahrzeugs in die Tabelle 'fahrzeuge'
    public function insertVehicleIntoFahrzeuge($vehicle) {
        $stmt = $this->conn->prepare("INSERT INTO fahrzeuge (barcode, barcode8, abteilung, fgNummer, marke, modell, farbe, aufnahmebereich, liste_id, bildPfad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $vehicle['barcode'], $vehicle['barcode8'], $vehicle['abteilung'], $vehicle['fgNummer'], $vehicle['marke'], $vehicle['modell'], $vehicle['farbe'], $vehicle['aufnahmebereich'], $vehicle['liste_id'], $vehicle['bildPfad']);
        
        if ($stmt->execute()) {
            return true; // Erfolgreiches Einfügen
        } else {
            $this->error = $stmt->error; // Fehler speichern
            return false; // Fehlgeschlagenes Einfügen
        }
    }

    // Methode zum Erstellen eines neuen Fahrzeugs in der Tabelle 'fahrzeuge'
    public function createVehicle($data) {
        $stmt = $this->conn->prepare("INSERT INTO fahrzeuge (barcode, barcode8, abteilung, fgNummer, marke, modell, farbe, aufnahmebereich, bildNummer, liste_id, bildPfad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $data['barcode'], $data['barcode8'], $data['abteilung'], $data['fgNummer'], $data['marke'], $data['modell'], $data['farbe'], $data['aufnahmebereich'], $data['bildNummer'], $data['liste_id'], $data['bildPfad']);
        
        if ($stmt->execute()) {
            return true; // Erfolgreiches Erstellen
        } else {
            $this->error = $stmt->error; // Fehler speichern
            return false; // Fehlgeschlagenes Erstellen
        }
    }

    // Methode zum Aktualisieren eines Fahrzeugs in der Tabelle 'fahrzeuge'
    public function updateVehicle($data) {
        $stmt = $this->conn->prepare("UPDATE fahrzeuge SET barcode = ?, barcode8 = ?, abteilung = ?, fgNummer = ?, marke = ?, modell = ?, farbe = ?, aufnahmebereich = ?, bildPfad = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $data['barcode'], $data['barcode8'], $data['abteilung'], $data['fgNummer'], $data['marke'], $data['modell'], $data['farbe'], $data['aufnahmebereich'], $data['bildPfad'], $data['id']);
        
        if ($stmt->execute()) {
            return true; // Erfolgreiches Aktualisieren
        } else {
            $this->error = $stmt->error; // Fehler speichern
            return false; // Fehlgeschlagenes Aktualisieren
        }
    }

    // Methode zum Löschen eines Fahrzeugs in der Tabelle 'fahrzeuge'
    public function deleteVehicle($id) {
        $stmt = $this->conn->prepare("DELETE FROM fahrzeuge WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true; // Erfolgreiches Löschen
        } else {
            $this->error = $stmt->error; // Fehler speichern
            return false; // Fehlgeschlagenes Löschen
        }
    }

    // Methode zum Abrufen eines Fahrzeugs anhand der ID aus der Tabelle 'fahrzeuge'
    public function getVehicleById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Rückgabe des gefundenen Fahrzeugs als assoziatives Array
    }

    // Methode zum Abrufen aller Fahrzeuge anhand der Listen-ID aus der Tabelle 'fahrzeuge'
    public function getByListId($liste_id) {
        $stmt = $this->conn->prepare("SELECT * FROM fahrzeuge WHERE liste_id = ?");
        $stmt->bind_param("i", $liste_id);
        $stmt->execute();
        return $stmt->get_result(); // Rückgabe der gefundenen Fahrzeuge als Ergebnisobjekt
    }

     public function getVehiclesWithListNumber() {
        $sql = "SELECT l.listeNummer, f.bildPfad, f.barcode8, f.fgNummer, f.marke, f.modell, f.farbe 
                FROM fahrzeuge f
                JOIN listen l ON f.liste_id = l.id
                ORDER BY l.listeNummer";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Methode zum Abrufen des letzten Fehlers
    public function getError() {
        return $this->error;
    }
}
?>
