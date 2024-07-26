<?php
// Konfigurationsdatei einbinden
include_once __DIR__ . '/../../config/config.php';

// Setze den Seitentitel auf "Neue Liste anlegen"
$title = "Neue Liste anlegen";

// Binde den Header ein, der wahrscheinlich den HTML-Kopfbereich und grundlegende Layouts enthält
include BASE_PATH . 'views/layouts/header.php';

// Binde den ListController ein, um Listen zu verwalten
include_once BASE_PATH . 'controllers/ListController.php';

// Starte die Session, falls sie noch nicht gestartet wurde
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Initialisiere den ListController mit der Datenbankverbindung
$listController = new ListController($conn);

// Überprüfe, ob das Formular abgesendet wurde (POST-Methode)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Hole die Daten aus dem Formular
    $ansager = $_POST['ansager'];
    $schreiber = $_POST['schreiber'];
    $filiale = $_POST['filiale'];
    $benutzer = $_SESSION['username']; // Angemeldeter Benutzer
    $listeNummer = $_POST['listeNummer'];

    // Erstelle eine neue Liste und speichere die ID der neu erstellten Liste
    $liste_id = $listController->create($ansager, $schreiber, $filiale, $benutzer, $listeNummer);
    
    // Leite den Benutzer zur Seite "show.php" weiter und übergebe die Listen-ID
    header("Location: " . BASE_URL . "lists/show?liste_id=$liste_id");
    exit;
} else {
    // Falls das Formular nicht abgesendet wurde, ermittle die nächste Listen-Nummer
    $username = $_SESSION['username'];
    $sql = "SELECT MAX(listeNummer) AS maxListeNummer FROM listen WHERE benutzer = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $neueListeNummer = $row['maxListeNummer'] + 1;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Liste anlegen</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/css.css">
</head>
<body>
<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                <h1>Neue Liste anlegen</h1>
            </div>

            <div class="content bg-white p-4 rounded shadow-sm">
                <form action="<?php echo BASE_URL; ?>lists/create" method="post">
                    <div class="form-group mb-3">
                        <label for="ansager">Ansager:</label>
                        <input type="text" class="form-control" id="ansager" name="ansager" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="schreiber">Schreiber:</label>
                        <input type="text" class="form-control" id="schreiber" name="schreiber" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filiale">Filiale:</label>
                        <input type="text" class="form-control" id="filiale" name="filiale" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="listeNummer">Listen Nummer:</label>
                        <input type="text" class="form-control" id="listeNummer" name="listeNummer" value="<?php echo htmlspecialchars($neueListeNummer); ?>" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Speichern</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
