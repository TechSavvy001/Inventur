<?php
// Bindet die Konfigurationsdatei ein, die die Datenbankverbindung enthält
include_once __DIR__ . '/../../config/config.php';

// Bindet den UserController ein, um Benutzeraktionen zu verwalten
include_once BASE_PATH . 'controllers/UserController.php';

// Initialisiert den UserController mit der Datenbankverbindung
$userController = new UserController($conn);

// Führt die Abmeldung des Benutzers durch
$userController->logout();

