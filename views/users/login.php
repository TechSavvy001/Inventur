<?php
session_start();
include_once '../../config/config.php';
include_once '../../controllers/UserController.php';

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
        $username = htmlspecialchars($_POST['username']);
        $password = $_POST['password'];

        $userController = new UserController($conn);
        $message = $userController->login($username, $password);
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../public/assets/css/css.css">
</head>
<body>
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
    <?php include '../layouts/footer.php'; ?>
</body>
</html>
