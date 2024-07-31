<?php
class ListModel {
    private $conn; // Datenbankverbindung

    // Konstruktor, um die Datenbankverbindung zu initialisieren
    public function __construct($conn) {
        $this->conn = $conn;
    }

// Methode zum Erstellen eines neuen Eintrags in der Tabelle "listen"
public function create($ansager, $schreiber, $filiale, $benutzer) {
    // Erstellen der neuen Liste ohne listeNummer
    $sql = "INSERT INTO listen (ansager, schreiber, filiale, benutzer) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssss", $ansager, $schreiber, $filiale, $benutzer);
    $stmt->execute();

    // Abrufen der ID der neu erstellten Liste
    $insert_id = $stmt->insert_id;

    // Aktualisieren der listeNummer mit der gleichen ID
    $update_sql = "UPDATE listen SET listeNummer = ? WHERE id = ?";
    $update_stmt = $this->conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $insert_id, $insert_id);
    $update_stmt->execute();

    // Rückgabe der ID der neu erstellten Liste
    return $insert_id;
    }

    // Methode, um alle Listen eines bestimmten Benutzers abzurufen
    public function getAll($username) {
        $sql = "SELECT * FROM listen WHERE benutzer = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde den Benutzernamen an die vorbereitete Anweisung
        $stmt->bind_param("s", $username);
        // Führe die Anweisung aus
        $stmt->execute();
        // Gib das Ergebnis der Abfrage zurück
        return $stmt->get_result();
    }

    // Methode, um eine bestimmte Liste anhand ihrer ID abzurufen
    public function getById($id) {
        $sql = "SELECT * FROM listen WHERE id = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde die ID an die vorbereitete Anweisung
        $stmt->bind_param("i", $id);
        // Führe die Anweisung aus
        $stmt->execute();
        // Gib das Ergebnis der Abfrage als assoziatives Array zurück
        return $stmt->get_result()->fetch_assoc();
    }

    // Methode, um eine vorhandene Liste zu aktualisieren
    public function update($id, $ansager, $schreiber, $filiale) {
        $sql = "UPDATE listen SET ansager = ?, schreiber = ?, filiale = ? WHERE id = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde die Parameter an die vorbereitete Anweisung
        $stmt->bind_param("sssi", $ansager, $schreiber, $filiale, $id);
        // Führe die Anweisung aus und gib das Ergebnis zurück (true bei Erfolg, false bei Fehler)
        return $stmt->execute();
    }

    // Methode, um eine bestimmte Liste zu löschen
    public function delete($id) {
        $sql = "DELETE FROM listen WHERE id = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Überprüfe, ob die Anweisung korrekt vorbereitet wurde
        if ($stmt === false) {
            $this->error = $this->conn->error;
            return false;
        }
        // Binde die ID an die vorbereitete Anweisung
        $stmt->bind_param("i", $id);
        // Führe die Anweisung aus und gib das Ergebnis zurück (true bei Erfolg, false bei Fehler)
        return $stmt->execute();
    }

    // Methode, um alle Fahrzeuge einer bestimmten Liste abzurufen
    public function getVehiclesByListId($liste_id) {
        $sql = "SELECT * FROM fahrzeuge WHERE liste_id = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde die Listen-ID an die vorbereitete Anweisung
        $stmt->bind_param("i", $liste_id);
        // Führe die Anweisung aus
        $stmt->execute();
        // Gib das Ergebnis der Abfrage zurück
        return $stmt->get_result();
    }
}
?>
