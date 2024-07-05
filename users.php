<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css.css">
</head>

<body>
    <?php
    session_start();
    include 'config.php';

    // Überprüfen, ob der Benutzer angemeldet ist
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }

    $message = '';

    // Benutzer hinzufügen
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "INSERT INTO login (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute() === TRUE) {
            $message = "<p class='alert alert-success'>Benutzer erfolgreich hinzugefügt</p>";
        } else {
            $message = "<p class='alert alert-danger'>Fehler: " . $stmt->error . "</p>";
        }
    }

    // Benutzer löschen
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];

        $sql = "DELETE FROM login WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute() === TRUE) {
            $message = "<p class='alert alert-success'>Benutzer erfolgreich gelöscht</p>";
        } else {
            $message = "<p class='alert alert-danger'>Fehler: " . $stmt->error . "</p>";
        }
    }

    // Benutzer aktualisieren
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "UPDATE login SET username = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $password, $id);

        if ($stmt->execute() === TRUE) {
            $message = "<p class='alert alert-success'>Benutzer erfolgreich aktualisiert</p>";
        } else {
            $message = "<p class='alert alert-danger'>Fehler: " . $stmt->error . "</p>";
        }
    }

    // SQL-Abfrage zum Abrufen aller Benutzer
    $sql = "SELECT * FROM login";
    $result = $conn->query($sql);

    $conn->close();
    ?>

    <div class="container mt-5">
        <div class="menubar bg-white shadow-sm py-2 px-4">
            <h1 class="h11">Benutzerverwaltung</h1>
        </div>

        <?php if (!empty($message)): ?>
            <div id="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="content mt-4">
            <div class="header p-3 mb-4 bg-white rounded shadow-sm">
                <h2>Neuen Benutzer hinzufügen</h2>
                <form action="users.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="username">Benutzername:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Passwort:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>

            <div class="content mt-4">
                <div class="header p-3 mb-4 bg-white rounded shadow-sm">
                    <h2>Benutzer bearbeiten</h2>
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Benutzername</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $result->fetch_assoc()): ?>
                                        <tr>
                                            <form action="users.php" method="post">
                                                <td><input type="text" class="form-control" name="username"
                                                        value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                                                <td><input type="password" class="form-control" name="password"
                                                        placeholder="Neues Passwort eingeben"></td>
                                                <td>
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <input type="hidden" name="action" value="update">
                                                    <button type="submit" class="btn btn-success btn-sm">Speichern</button>
                                                    <a href="users.php?delete=<?php echo $user['id']; ?>"
                                                        onclick="return confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?')"
                                                        class="btn btn-danger btn-sm">Löschen</a>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="alert alert-warning">Keine Benutzer gefunden.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
        <script>
            // Meldung nach 3 Sekunden ausblenden
            setTimeout(function() {
                const messageElement = document.getElementById('message');
                if (messageElement) {
                    messageElement.style.display = 'none';
                }
            }, 3000);
        </script>
</body>

</html>
