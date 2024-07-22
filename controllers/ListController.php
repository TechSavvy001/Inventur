<?php
include_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/models/ListModel.php';
include_once dirname(__DIR__) . '/models/VehicleModel.php';

class ListController {
    private $listModel;
    private $vehicleModel;

    public function __construct($db) {
        $this->listModel = new ListModel($db);
        $this->vehicleModel = new VehicleModel($db);
    }

    public function create($ansager, $schreiber, $filiale, $benutzer, $listeNummer) {
        return $this->listModel->create($ansager, $schreiber, $filiale, $benutzer, $listeNummer);
    }

    public function handleCreateRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['username'])) {
                header('Location: ../users/login.php');
                exit;
            }
            $benutzer = $_SESSION['username'];
            $ansager = $_POST['ansager'];
            $schreiber = $_POST['schreiber'];
            $filiale = $_POST['filiale'];
            $listeNummer = $_POST['listeNummer'];

            $liste_id = $this->create($ansager, $schreiber, $filiale, $benutzer, $listeNummer);
            header("Location: show.php?liste_id=$liste_id");
            exit;
        } else {
            include '../views/lists/create.php';
        }
    }

    public function getListDetails($liste_id) {
        return $this->listModel->getById($liste_id);
    }

    public function getVehiclesByListId($liste_id) {
        return $this->vehicleModel->getByListId($liste_id);
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['id'];
            $ansager = $_POST['ansager'];
            $schreiber = $_POST['schreiber'];
            $filiale = $_POST['filiale'];

            if ($this->listModel->update($id, $ansager, $schreiber, $filiale)) {
                session_start();
                $_SESSION['success_message'] = "Die Liste wurde erfolgreich aktualisiert.";
                header("Location: ../views/lists/show.php?liste_id=$id");
                exit();
            } else {
                echo "Fehler beim Aktualisieren der Liste.";
            }
        }
    }

    public function delete($id) {
        // Überprüfen, ob die Liste Fahrzeuge hat
        $vehicles = $this->vehicleModel->getByListId($id);
        if ($vehicles->num_rows > 0) {
            session_start();
            $_SESSION['error_message'] = "Die Liste kann nicht gelöscht werden, da sie noch Fahrzeuge enthält. Bitte löschen Sie zuerst alle Fahrzeuge.";
            header("Location: ../views/lists/index.php");
            exit();
        }

        if ($this->listModel->delete($id)) {
            session_start();
            $_SESSION['success_message'] = "Die Liste wurde erfolgreich gelöscht.";
            header("Location: ../views/lists/index.php");
            exit();
        } else {
            session_start();
            $_SESSION['error_message'] = "Fehler beim Löschen der Liste.";
            header("Location: ../views/lists/index.php");
            exit();
        }
    }

    public function getById($id) {
        return $this->listModel->getById($id);
    }

    public function getAll($username) {
        return $this->listModel->getAll($username);
    }
}

$controller = new ListController($conn);

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete($_GET['id']);
            break;
        // Weitere Aktionen hier hinzufügen, falls erforderlich
    }
}
?>
