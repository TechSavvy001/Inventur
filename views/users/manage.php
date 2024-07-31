<?php
// Startet die Session
session_start();

// Zeigt alle Fehler an
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Setzt den Seitentitel auf "Benutzerverwaltung"
$title = "Benutzerverwaltung";

// Definiere BASE_PATH falls es nicht definiert ist
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../../');
}

// Definiere BASE_URL falls es nicht definiert ist
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Inventur/');
}

// Überprüfe, ob der Benutzer angemeldet ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'views/users/login.php');
    exit;
}

// Überprüfe, ob der Benutzer ein Administrator ist
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ' . BASE_URL . 'lists/start');
    exit;
}

// Bindet die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once BASE_PATH . 'config/config.php';

include BASE_PATH . 'views/layouts/header.php';

// Bindet den UserController ein, um Benutzeraktionen zu verwalten
include_once BASE_PATH . 'controllers/UserController.php';

// Initialisiert den UserController mit der Datenbankverbindung
$userController = new UserController($conn);

// Variable für Nachrichten (Erfolgs- oder Fehlermeldungen)
$message = '';

// Benutzer hinzufügen
// Überprüfe, ob ein Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hinzufügen eines neuen Benutzers
    if ($_POST['action'] === 'add') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        if ($userController->addUser($username, $password, $role)) {
            $message = 'Benutzer erfolgreich hinzugefügt!';
        } else {
            $message = 'Fehler beim Hinzufügen des Benutzers!';
        }
    }

    // Bearbeiten eines Benutzers
    elseif ($_POST['action'] === 'update') {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        if ($userController->updateUser($id, $username, $password, $role)) {
            $message = 'Benutzer erfolgreich bearbeitet!';
        } else {
            $message = 'Fehler beim Bearbeiten des Benutzers!';
        }
    }

    // Löschen eines Benutzers
    elseif ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        if ($userController->deleteUser($id)) {
            $message = 'Benutzer erfolgreich gelöscht!';
        } else {
            $message = 'Fehler beim Löschen des Benutzers!';
        }
    }
}

// Alle Benutzer abrufen
$users = $userController->getAllUsers();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/css.css">
</head>
<body>
<div class="container mt-5">
    <div class="menubar bg-white shadow-sm py-2 px-4">
        <h1 class="h11">Benutzerverwaltung</h1>
    </div>
    <?php if (!empty($message)): ?>
        <div id="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="content mt-4">
        <div class="header p-3 mb-4 bg-white rounded shadow-sm">
            <h2>Neuen Benutzer hinzufügen</h2>
            <form action="<?php echo BASE_URL; ?>manage" method="post">

                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="username">Benutzername:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Rolle:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="Mitarbeiter">Mitarbeiter</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
        <div class="content mt-4">
            <div class="header p-3 mb-4 bg-white rounded shadow-sm">
                <h2>Benutzer bearbeiten</h2>
                <?php if (count($users) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Benutzername</th>
                                    <th>Rolle</th>
                                    <th>Passwort</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <form action="<?php echo BASE_URL; ?>manage" method="post">

                                            <td><input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                                            <td>
                                                <select class="form-control" name="role" required>
                                                    <option value="Mitarbeiter" <?php if($user['role'] == 'Mitarbeiter') echo 'selected'; ?>>Mitarbeiter</option>
                                                    <option value="Admin" <?php if($user['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                                                </select>
                                            </td>
                                            <td><input type="password" class="form-control" name="password" placeholder="Neues Passwort eingeben"></td>
                                            <td>
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="update">
                                                <button type="submit" class="btn btn-success btn-sm">Speichern</button>
                                        </form>
                                        <!-- Separates Formular für den Lösch-Button -->
                                        <form action="<?php echo BASE_URL; ?>manage" method="post" style="display:inline;">

                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?')">Löschen</button>
                                        </form>
                                            </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="mt-4">
                        <a href="<?php echo BASE_URL; ?>lists/start" class="btn btn-primary">Zurück</a>
                    </div>

                    </div>
                <?php else: ?>
                    <p class="alert alert-warning">Keine Benutzer gefunden.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
    <script>
        // Meldung nach 3 Sekunden ausblenden
        setTimeout(function() {
            const messageElement = document.getElementById('message');
            if (messageElement) {
                messageElement.style.display = 'none';
            }
        }, 3000);
    </script>
    <?php include BASE_PATH . 'views/layouts/footer.php'; ?>
</body>
</html>
