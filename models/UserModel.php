<?php
class UserModel {
    private $conn; // Datenbankverbindung

    // Konstruktor, um die Datenbankverbindung zu initialisieren
    public function __construct($db) {
        $this->conn = $db;
    }

    // Methode, um einen neuen Benutzer hinzuzufügen
    public function addUser($username, $password) {
        $sql = "INSERT INTO login (username, password) VALUES (?, ?)";
        // Bereite die SQL-Anweisung vor, um SQL-Injections zu verhindern
        $stmt = $this->conn->prepare($sql);
        // Binde die Parameter an die vorbereitete Anweisung
        $stmt->bind_param("ss", $username, $password);
        // Führe die Anweisung aus und gib das Ergebnis zurück (true bei Erfolg, false bei Fehler)
        return $stmt->execute();
    }

    // Methode, um einen Benutzer zu löschen
    public function deleteUser($id) {
        $sql = "DELETE FROM login WHERE id = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde die ID an die vorbereitete Anweisung
        $stmt->bind_param("i", $id);
        // Führe die Anweisung aus und gib das Ergebnis zurück (true bei Erfolg, false bei Fehler)
        return $stmt->execute();
    }

    // Methode, um einen Benutzer zu aktualisieren
    public function updateUser($id, $username, $password) {
        $sql = "UPDATE login SET username = ?, password = ? WHERE id = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde die Parameter an die vorbereitete Anweisung
        $stmt->bind_param("ssi", $username, $password, $id);
        // Führe die Anweisung aus und gib das Ergebnis zurück (true bei Erfolg, false bei Fehler)
        return $stmt->execute();
    }

    // Methode, um alle Benutzer abzurufen
    public function getAllUsers() {
        $sql = "SELECT * FROM login";
        // Führe die Abfrage aus
        $result = $this->conn->query($sql);
        // Gib das Ergebnis als assoziatives Array zurück
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Methode, um einen Benutzer anhand des Benutzernamens abzurufen
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM login WHERE username = ?";
        // Bereite die SQL-Anweisung vor
        $stmt = $this->conn->prepare($sql);
        // Binde den Benutzernamen an die vorbereitete Anweisung
        $stmt->bind_param("s", $username);
        // Führe die Anweisung aus
        $stmt->execute();
        // Gib das Ergebnis der Abfrage als assoziatives Array zurück
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
