<?php
// Setze den Seitentitel auf "Start"
$title = "Start";
// Binde die Header-Datei ein, die wahrscheinlich den HTML-Kopfbereich enthält
include '../views/layouts/header.php';
?>

<body>
    <?php
    // Überprüfe, ob die PHP-Sitzung noch nicht gestartet wurde, und starte sie, falls nötig
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Überprüfe, ob der Benutzer eingeloggt ist
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // Wenn der Benutzer nicht eingeloggt ist, leite ihn zur Login-Seite weiter
        header('Location: ../views/login.php');
        exit;
    }

    // Binde die Konfigurationsdatei ein, die wahrscheinlich die Datenbankverbindung enthält
    include '../config/config.php';
    
    // Ermittel die nächste Listen-Nummer für den aktuellen Benutzer
    $username = $_SESSION['username'];
    $sql = "SELECT MAX(listeNummer) AS maxListeNummer FROM listen WHERE benutzer = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    // Erhöhe die maximale Listen-Nummer um 1, um die neue Listen-Nummer zu erhalten
    $neueListeNummer = $row['maxListeNummer'] + 1;
    ?>

    <!-- Hauptinhalt der Seite -->
    <div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                    <h1>Aktion wählen</h1>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm text-center">
                    <!-- Formular zum Erstellen einer neuen Liste -->
                    <form action="../views/lists/create.php" method="get">
                        <input type="hidden" name="listeNummer" value="<?php echo $neueListeNummer; ?>">
                        <button type="submit" class="btn btn-primary mb-3 w-100">Neue Liste anlegen</button>
                    </form>
                    <!-- Link zum Bearbeiten vorhandener Listen -->
                    <a href="../views/lists/index.php" class="btn btn-secondary w-100">Listen bearbeiten</a>
                    <br><br>
                </div>
            </div>
        </div>
    </div>

    <!-- Einbinden von Bootstrap-JavaScript-Dateien -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
