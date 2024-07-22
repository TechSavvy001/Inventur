<?php
session_start();
$title = "Listen Übersicht";
include '../layouts/header.php';
include_once '../../controllers/ListController.php';
include_once '../../config/config.php';

$listController = new ListController($conn);
$username = $_SESSION['username'];
$lists = $listController->getAll($username);

// Erfolgsmeldung oder Fehlermeldung abfangen
$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listen Übersicht</title>
    <link rel="stylesheet" href="../../public/assets/css/css.css">
</head>
<body>
<div class="container-fluid mt-5" style="max-width: 90%; margin: 0 auto;">
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-12">
            <div class="menubar bg-white shadow-sm py-2 px-4">
                <h1>Listen Übersicht</h1>
            </div>

            <div class="content bg-white p-4 rounded shadow-sm mt-4">
                <?php if ($lists->num_rows > 0): ?>
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
                                <?php while ($list = $lists->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($list['listeNummer']); ?></td>
                                        <td><?php echo htmlspecialchars($list['ansager']); ?></td>
                                        <td><?php echo htmlspecialchars($list['schreiber']); ?></td>
                                        <td><?php echo htmlspecialchars($list['filiale']); ?></td>
                                        <td>
                                            <a href="show.php?liste_id=<?php echo $list['id']; ?>" class="btn btn-secondary btn-sm">Bearbeiten</a>
                                            <a href="../../controllers/ListController.php?action=delete&id=<?php echo $list['id']; ?>" class="btn btn-danger btn-sm">Löschen</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="alert alert-warning">Keine Listen gefunden.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include '../layouts/footer.php'; ?>
</body>
</html>
