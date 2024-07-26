<?php
// Startet die Session
session_start();

// Zeigt alle Fehler an
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    // Falls nicht, leite den Benutzer zur Login-Seite weiter
    header('Location: ' . BASE_URL . 'views/users/login.php');
    exit;
}

// Bindet die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once BASE_PATH . 'config/config.php';

// Bindet den UserController ein, um Benutzeraktionen zu verwalten
include_once BASE_PATH . 'controllers/UserController.php';

// Initialisiert den UserController mit der Datenbankverbindung
$userController = new UserController($conn);

// Variable für Nachrichten (Erfolgs- oder Fehlermeldungen)
$message = '';

// Benutzer hinzufügen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($userController->addUser($username, $password)) {
        $message = "<p class='alert alert-success'>Benutzer erfolgreich hinzugefügt</p>";
    } else {
        $message = "<p class='alert alert-danger'>Fehler: Benutzer konnte nicht hinzugefügt werden.</p>";
    }
}

// Benutzer löschen
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    if ($userController->deleteUser($id)) {
        $message = "<p class='alert alert-success'>Benutzer erfolgreich gelöscht</p>";
    } else {
        $message = "<p class='alert alert-danger'>Fehler: Benutzer konnte nicht gelöscht werden.</p>";
    }
}

// Benutzer aktualisieren
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($userController->updateUser($id, $username, $password)) {
        $message = "<p class='alert alert-success'>Benutzer erfolgreich aktualisiert</p>";
    } else {
        $message = "<p class='alert alert-danger'>Fehler: Benutzer konnte nicht aktualisiert werden.</p>";
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
<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
    <div class="menubar bg-white shadow-sm py-2 px-4">
        <h1 class="h11">Benutzerverwaltung</h1>
    </div>
    <?php if (!empty($message)): ?>
        <div id="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="content mt-4">
        <div class="header p-3 mb-4 bg-white rounded shadow-sm">
            <h2>Neuen Benutzer hinzufügen</h2>
            <form action="<?php echo BASE_URL; ?>views/users/manage.php" method="post">
            <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="username">Benutzername:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
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
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                            <td><input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                                            <td><input type="password" class="form-control" name="password" placeholder="Neues Passwort eingeben"></td>
                                            <td>
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="update">
                                                <button type="submit" class="btn btn-success btn-sm">Speichern</button>
                                                <a href="manage.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?')" class="btn btn-danger btn-sm">Löschen</a>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
