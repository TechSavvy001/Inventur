<?php
class UserModel {
    private $conn; // Datenbankverbindung

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM login WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

public function addUser($username, $password, $role) {
        $sql = "INSERT INTO login (username, password, role) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $role);
        return $stmt->execute();
    }
    
    public function isUsernameExists($username) {
        $sql = "SELECT COUNT(*) as count FROM login WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

public function deleteUser($id) {
        $sql = "DELETE FROM login WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        if ($result) {
            error_log("Delete query successful for user ID: $id");
        } else {
            error_log("Delete query failed for user ID: $id");
        }
        return $result;
    }

public function updateUser($id, $username, $password, $role) {
        $sql = "UPDATE login SET username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $role, $id);
        return $stmt->execute();
    }

public function getUserById($id) {
        $sql = "SELECT * FROM login WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

 public function getAllUsers() {
        $sql = "SELECT * FROM login";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}