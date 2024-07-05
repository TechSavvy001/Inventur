<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listen bearbeiten</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }

    include 'config.php';

    // SQL-Abfrage zum Abrufen aller Listen des Benutzers
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM listen WHERE benutzer = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="menubar bg-white shadow-sm py-2 px-4">
                    <h1 class="h4">Listen bearbeiten</h1>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm mt-4">
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Liste Nummer</th>
                                        <th>Ansager</th>
                                        <th>Schreiber</th>
                                        <th>Filiale</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($list = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($list['listeNummer']); ?></td>
                                            <td><?php echo htmlspecialchars($list['ansager']); ?></td>
                                            <td><?php echo htmlspecialchars($list['schreiber']); ?></td>
                                            <td><?php echo htmlspecialchars($list['filiale']); ?></td>
                                            <td>
                                                <a href="edit_list.php?id=<?php echo $list['id']; ?>"
                                                    class="btn btn-secondary btn-sm">Bearbeiten</a>
                                                <a href="delete_list.php?id=<?php echo $list['id']; ?>"
                                                    class="btn btn-danger btn-sm">Löschen</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <a href="auswahl.php" class="btn btn-primary">Go Back</a>

                    <?php else: ?>
                        <p class="alert alert-warning">Keine Listen gefunden.</p>
                        <a href="auswahl.php" class="btn btn-primary mt-3">Go Back</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
