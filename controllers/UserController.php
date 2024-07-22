<?php
include_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    public function login($username, $password) {
        $user = $this->userModel->getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header('Location: ../../public/index.php');
        } else {
            return "Ungültiger Benutzername oder Passwort.";
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'views/users/login.php');
        exit();
    }

    public function addUser($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
        return $this->userModel->addUser($username, $hashedPassword);
    }

    public function deleteUser($id) {
        return $this->userModel->deleteUser($id);
    }

    public function updateUser($id, $username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
        return $this->userModel->updateUser($id, $username, $hashedPassword);
    }

    public function getAllUsers() {
        return $this->userModel->getAllUsers();
    }
}