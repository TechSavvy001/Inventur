<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ansager = $_POST['ansager'];
    $schreiber = $_POST['schreiber'];
    $filiale = $_POST['filiale'];
    $benutzer = $_POST['benutzer'];
    $listeNummer = $_POST['listeNummer'];

    $username = $_SESSION['username'];

    // Neue Liste erstellen mit fortlaufender Listen-Nr.
    $sql = "INSERT INTO listen (ansager, schreiber, filiale, benutzer, listeNummer) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $ansager, $schreiber, $filiale, $username, $listeNummer);
    if ($stmt->execute() === TRUE) {
        $liste_id = $stmt->insert_id;
        header("Location: aufnahmelisten.php?liste_id=$liste_id");
        exit;
    } else {
        echo "<p class='alert alert-danger'>Fehler beim Erstellen der Liste: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fehler beim Speichern</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                    <h1 class="h4">Fehler beim Speichern</h1>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm text-center">
                    <p class='alert alert-danger'>Fehler beim Erstellen der Liste: <?php echo $stmt->error; ?></p>
                    <a href="eingabe.php" class="btn btn-primary">Zurück</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
