<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerdaten eingeben</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css.css">

</head>

<body>
    <?php
    session_start();
    include 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ansager = $_POST['ansager'];
        $schreiber = $_POST['schreiber'];
        $filiale = $_POST['filiale'];
        $benutzer = $_POST['benutzer'];

        // Neue Liste erstellen
        $sql = "INSERT INTO listen (ansager, schreiber, filiale, benutzer) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $ansager, $schreiber, $filiale, $benutzer);
        $stmt->execute();

        // ID der neu erstellten Liste abrufen
        $liste_id = $stmt->insert_id;

        // Listen-Nummer auf die ID setzen
        $sql = "UPDATE listen SET listeNummer = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $liste_id, $liste_id);
        $stmt->execute();

        header("Location: aufnahmelisten.php?liste_id=$liste_id");
        exit;
    }
    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                    <h2>Benutzerdaten eingeben</h2>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm">
                    <form action="eingabe.php" method="post">
                        <div class="mb-3">
                            <label for="ansager" class="form-label">Ansager:</label>
                            <input type="text" class="form-control" id="ansager" name="ansager" required>
                        </div>

                        <div class="mb-3">
                            <label for="schreiber" class="form-label">Schreiber:</label>
                            <input type="text" class="form-control" id="schreiber" name="schreiber" required>
                        </div>

                        <div class="mb-3">
                            <label for="filiale" class="form-label">Filiale:</label>
                            <input type="text" class="form-control" id="filiale" name="filiale" required>
                        </div>

                        <div class="mb-3">
                            <label for="benutzer" class="form-label">Benutzer:</label>
                            <input type="text" class="form-control" id="benutzer" name="benutzer" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Speichern</button>
                        <a href="auswahl.php" class="btn btn-secondary">Go Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
