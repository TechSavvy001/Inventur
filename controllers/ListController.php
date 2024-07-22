<?php
// Einbinden der notwendigen Konfigurations- und Modell-Dateien
include_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/models/ListModel.php';
include_once dirname(__DIR__) . '/models/VehicleModel.php';

class ListController {
    private $listModel;  // Instanz des ListModel
    private $vehicleModel;  // Instanz des VehicleModel

    // Konstruktor, der die Modelle initialisiert
    public function __construct($db) {
        $this->listModel = new ListModel($db);
        $this->vehicleModel = new VehicleModel($db);
    }

    // Methode zum Erstellen einer neuen Liste
    public function create($ansager, $schreiber, $filiale, $benutzer, $listeNummer) {
        return $this->listModel->create($ansager, $schreiber, $filiale, $benutzer, $listeNummer);
    }

    // Methode zur Verarbeitung eines Create-Requests
    public function handleCreateRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {  // Überprüfen, ob es sich um einen POST-Request handelt
            if (session_status() == PHP_SESSION_NONE) {
                session_start();  // Sitzung starten, falls noch nicht gestartet
            }
            if (!isset($_SESSION['username'])) {  // Überprüfen, ob der Benutzer eingeloggt ist
                header('Location: ../users/login.php');
                exit;
            }
            // POST-Daten abrufen
            $benutzer = $_SESSION['username'];
            $ansager = $_POST['ansager'];
            $schreiber = $_POST['schreiber'];
            $filiale = $_POST['filiale'];
            $listeNummer = $_POST['listeNummer'];

            // Neue Liste erstellen und zur Detailansicht weiterleiten
            $liste_id = $this->create($ansager, $schreiber, $filiale, $benutzer, $listeNummer);
            header("Location: show.php?liste_id=$liste_id");
            exit;
        } else {
            include '../views/lists/create.php';  // Formular anzeigen, wenn es kein POST-Request ist
        }
    }

    // Methode zum Abrufen der Details einer Liste anhand der ID
    public function getListDetails($liste_id) {
        return $this->listModel->getById($liste_id);
    }

    // Methode zum Abrufen aller Fahrzeuge einer Liste anhand der Listen-ID
    public function getVehiclesByListId($liste_id) {
        return $this->vehicleModel->getByListId($liste_id);
    }

    // Methode zum Aktualisieren einer Liste
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {  // Überprüfen, ob es sich um einen POST-Request handelt
            $id = $_POST['id'];
            $ansager = $_POST['ansager'];
            $schreiber = $_POST['schreiber'];
            $filiale = $_POST['filiale'];

            // Liste aktualisieren und zur Detailansicht weiterleiten
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

    // Methode zum Löschen einer Liste
    public function delete($id) {
        // Überprüfen, ob die Liste Fahrzeuge hat
        $vehicles = $this->vehicleModel->getByListId($id);
        if ($vehicles->num_rows > 0) {  // Wenn die Liste Fahrzeuge enthält, löschen verhindern
            session_start();
            $_SESSION['error_message'] = "Die Liste kann nicht gelöscht werden, da sie noch Fahrzeuge enthält. Bitte löschen Sie zuerst alle Fahrzeuge.";
            header("Location: ../views/lists/index.php");
            exit();
        }

        // Liste löschen und zur Übersicht weiterleiten
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

    // Methode zum Abrufen einer Liste anhand der ID
    public function getById($id) {
        return $this->listModel->getById($id);
    }

    // Methode zum Abrufen aller Listen eines Benutzers
    public function getAll($username) {
        return $this->listModel->getAll($username);
    }
}

// Erstellen einer Instanz des ListController
$controller = new ListController($conn);

// Überprüfen, ob eine Aktion angegeben ist und entsprechende Methode aufrufen
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
