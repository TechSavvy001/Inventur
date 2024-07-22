<?php
class ListModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($ansager, $schreiber, $filiale, $benutzer, $listeNummer) {
        $sql = "INSERT INTO listen (ansager, schreiber, filiale, benutzer, listeNummer) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $ansager, $schreiber, $filiale, $benutzer, $listeNummer);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function getAll($username) {
        $sql = "SELECT * FROM listen WHERE benutzer = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getById($id) {
        $sql = "SELECT * FROM listen WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $ansager, $schreiber, $filiale) {
        $sql = "UPDATE listen SET ansager = ?, schreiber = ?, filiale = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $ansager, $schreiber, $filiale, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM listen WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->error = $this->conn->error;
            return false;
        }
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getVehiclesByListId($liste_id) {
        $sql = "SELECT * FROM fahrzeuge WHERE liste_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $liste_id);
        $stmt->execute();
        return $stmt->get_result();
    }

}
?>
