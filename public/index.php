<?php
$title = "Start";
include '../views/layouts/header.php';
?>


<body>
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: ../views/login.php');
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

<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                    <h1>Aktion wählen</h1>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm text-center">
                    <form action="../views/lists/create.php" method="get">
                        <input type="hidden" name="listeNummer" value="<?php echo $neueListeNummer; ?>">
                        <button type="submit" class="btn btn-primary mb-3 w-100">Neue Liste anlegen</button>
                    </form>
                    <a href="../views/lists/index.php" class="btn btn-secondary w-100">Listen bearbeiten</a>
                    <br><br>
                   <!-- <a href="../views/login.php" class="btn btn-primary">Go Back</a>-->
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
