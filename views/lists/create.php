<?php
$title = "Neue Liste anlegen";
include '../layouts/header.php';
include_once '../../controllers/ListController.php';
include_once '../../config/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../views/users/login.php');
    exit;
}

$listController = new ListController($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ansager = $_POST['ansager'];
    $schreiber = $_POST['schreiber'];
    $filiale = $_POST['filiale'];
    $benutzer = $_SESSION['username']; // Angemeldeter Benutzer
    $listeNummer = $_POST['listeNummer'];

    $liste_id = $listController->create($ansager, $schreiber, $filiale, $benutzer, $listeNummer);
    header("Location: show.php?liste_id=$liste_id");
    exit;
} else {
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
    <link rel="stylesheet" href="../../public/assets/css/css.css">
</head>
<body>
<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                <h1>Neue Liste anlegen</h1>
            </div>

            <div class="content bg-white p-4 rounded shadow-sm">
                <form action="../../views/lists/create.php" method="post">
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
