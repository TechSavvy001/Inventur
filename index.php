<?php
// Konfigurationsdatei einbinden
require_once 'config/config.php';

// Routing-Tabelle: URL-Pfade zu Dateipfaden
$routes = [
    'lists/create' => BASE_PATH . 'views/lists/create.php',
    'lists/edit' => BASE_PATH . 'views/lists/edit.php',
    'lists/index' => BASE_PATH . 'views/lists/index.php',
    'lists/show' => BASE_PATH . 'views/lists/show.php',
    'lists/start' => BASE_PATH . 'views/lists/start.php',
    'login' => BASE_PATH . 'views/users/login.php',
    'logout' => BASE_PATH . 'views/users/logout.php',
    'manage' => BASE_PATH . 'views/users/manage.php',
    'vehicles/create' => BASE_PATH . 'views/vehicles/create.php',
    'images' => BASE_PATH . 'views/images/index.php',
];

// Hole die angeforderte URL ohne die Query-Parameter
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

// Entferne die BASE_URL vom Anfang der angeforderten URI
$request_uri = str_replace(BASE_URL, '', $request_uri);

// Überprüfen, ob die angeforderte URL in der Routing-Tabelle existiert
if (array_key_exists($request_uri, $routes)) {
    // Dateipfad aus der Routing-Tabelle holen
    $file_to_include = $routes[$request_uri];

    // Prüfen, ob die Datei existiert, bevor sie eingebunden wird
    if (file_exists($file_to_include)) {
        include $file_to_include;
    } else {
        // Datei nicht gefunden
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
} else {
    // Wenn die Route nicht gefunden wird, eine 404-Fehlerseite anzeigen oder weiterleiten
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit;
}
