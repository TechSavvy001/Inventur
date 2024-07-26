<?php
// Einbinden der notwendigen Konfigurations- und Modell-Dateien
include_once dirname(__DIR__) . '/config/config.php';
include_once BASE_PATH . 'models/UserModel.php';

class UserController {
    private $userModel; // Instanz des UserModel

    // Konstruktor, der das UserModel initialisiert
    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    // Methode zum Einloggen eines Benutzers
    public function login($username, $password) {
        // Benutzer anhand des Benutzernamens abrufen
        $user = $this->userModel->getUserByUsername($username);

        // Überprüfen, ob Benutzer existiert und das Passwort korrekt ist
        if ($user && password_verify($password, $user['password'])) {
            // Sitzung starten und Sitzungsvariablen setzen
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            // Benutzer zur Hauptseite weiterleiten
            header('Location: ' . BASE_URL . 'lists/start');
            exit();
        } else {
            // Fehlermeldung zurückgeben, wenn Benutzername oder Passwort ungültig ist
            return "Ungültiger Benutzername oder Passwort.";
        }
    }

    // Methode zum Ausloggen eines Benutzers
    public function logout() {
        // Sitzung starten
        session_start();
        // Alle Sitzungsvariablen löschen
        session_unset();
        // Sitzung zerstören
        session_destroy();

        // Benutzer zur Login-Seite weiterleiten
        header('Location: ' . BASE_URL . 'login');
        exit();
    }

    // Methode zum Hinzufügen eines neuen Benutzers
    public function addUser($username, $password) {
        // Passwort hashen
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
        // Benutzer zur Datenbank hinzufügen
        return $this->userModel->addUser($username, $hashedPassword);
    }

    // Methode zum Löschen eines Benutzers anhand der ID
    public function deleteUser($id) {
        return $this->userModel->deleteUser($id);
    }

    // Methode zum Aktualisieren eines Benutzers
    public function updateUser($id, $username, $password) {
        // Passwort hashen
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
        // Benutzer in der Datenbank aktualisieren
        return $this->userModel->updateUser($id, $username, $hashedPassword);
    }

    // Methode zum Abrufen aller Benutzer
    public function getAllUsers() {
        return $this->userModel->getAllUsers();
    }
}
