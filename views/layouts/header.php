<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once dirname(__DIR__, 2) . '/config/config.php';

$base_url = rtrim(BASE_URL, '/') . '/';

// Überprüfe, ob der Benutzer angemeldet ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Falls nicht, leite den Benutzer zur Login-Seite weiter
    header('Location: ' . BASE_URL . 'views/users/login.php');
    exit;
}

$role = $_SESSION['role']; // Benutzerrolle aus der Session abrufen
$username = $_SESSION['username']; // Benutzername aus der Session abrufen

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Seite'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/css.css">
    <link rel="icon" href="<?php echo $base_url; ?>public/assets/images/logo.png" type="image/x-icon"> <!-- Favicon Link -->
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_url; ?>lists/start">Inventur</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-text ms-auto d-lg-none">
                Hallo <b><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></b>!
            </span>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>lists/start">Aktion wählen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>images">Bilder</a>
                    </li>
                    <?php if ($role == 'Admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>manage">Benutzerverwaltung</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>logout">Logout</a>
                    </li>
                </ul>
                    <span class="navbar-text ms-auto d-none d-lg-inline" style="display: flex; align-items: center;">
                        Hallo <b><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></b>!
                        <img src="<?php echo $base_url; ?>public/assets/images/bmwIcon.svg" alt="User Image" class="rounded-circle" style="width: 20px; height: 20px; margin-left: 5px;">
                </span>
            </div>
        </div>
    </nav>
</body>
</html>
