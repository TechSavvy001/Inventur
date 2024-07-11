<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
include '../config/config.php';

$message = '';

// CSRF-Token generieren
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF-Token überprüfen
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        $message = "Ungültiger CSRF-Token.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Eingaben validieren und desinfizieren
        $username = htmlspecialchars($username);

        $sql = "SELECT id, username, password FROM login WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                // Session-Fixation verhindern
                session_regenerate_id(true);

                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['id'] = $id;

                header('Location: auswahl.php');
                exit;
            } else {
                $message = "Ungültiger Benutzername oder Passwort.";
            }
        } else {
            $message = "Ungültiger Benutzername oder Passwort.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="menubar bg-white shadow-sm py-2 px-4 text-center mb-4">
                    <h1>Login</h1>
                </div>

                <div class="content bg-white p-4 rounded shadow-sm">
                    <form action="login.php" method="post">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
                        <div class="form-group mb-3">
                            <label for="username">Benutzername:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Passwort:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <?php if (!empty($message)): ?>
                        <p class="alert alert-danger mt-3"><?php echo $message; ?></p>
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