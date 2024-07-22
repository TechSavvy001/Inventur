<?php
class UserModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Benutzer hinzufügen
    public function addUser($username, $password) {
        $sql = "INSERT INTO login (username, password) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        return $stmt->execute();
    }

    // Benutzer löschen
    public function deleteUser($id) {
        $sql = "DELETE FROM login WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Benutzer aktualisieren
    public function updateUser($id, $username, $password) {
        $sql = "UPDATE login SET username = ?, password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $password, $id);
        return $stmt->execute();
    }

    // Alle Benutzer abrufen
    public function getAllUsers() {
        $sql = "SELECT * FROM login";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Benutzer nach Benutzername abrufen
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM login WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
