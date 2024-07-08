<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktion wählen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="css.css">
        
    </head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }

    include '../config/config.php';

    // Fortlaufende Listen-Nr. für den Benutzer ermitteln
    $username = $_SESSION['username'];
    $sql = "SELECT MAX(listeNummer) AS maxListeNummer FROM listen WHERE benutzer = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $neueListeNummer = $row['maxListeNummer'] + 1;

    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                    <h1>Aktion wählen</h1>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm text-center">
                    <form action="eingabe.php" method="get">
                        <input type="hidden" name="listeNummer" value="<?php echo $neueListeNummer; ?>">
                        <button type="submit" class="btn btn-primary mb-3 w-100">Neue Liste erstellen</button>
                    </form>
                    <a href="listen_bearbeiten.php" class="btn btn-secondary w-100">Listen bearbeiten</a>
                    <br><br>
                    <a href="login.php" class="btn btn-primary">Go Back</a>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
