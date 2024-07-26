<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Einbinden der Konfigurations- und Modell-Dateien
include_once dirname(__DIR__) . '/config/config.php'; 
include_once dirname(__DIR__) . '/models/VehicleModel.php';

// Definieren der Basis-URL, falls sie nicht bereits definiert ist
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Inventur/'); // Basis-URL relativ zur Root-Domain, passe dies bei Bedarf an
}

class VehicleController {
    private $model; // Instanz des VehicleModel

    // Konstruktor, der das VehicleModel initialisiert
    public function __construct($conn) {
        $this->model = new VehicleModel($conn);
    }

    // Methode zum Speichern eines neuen Fahrzeugs
    public function store() {
        session_start();
        
        // Überprüfen, ob der Benutzer eingeloggt ist
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header('Location: ../users/login.php');
            exit();
        }
    
        // Überprüfen, ob die Anfrage eine POST-Anfrage ist
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sammeln der Fahrzeugdaten aus dem POST-Array
            $data = [
                'barcode' => $_POST['barcode'],
                'barcode8' => $_POST['barcode8'],
                'abteilung' => $_POST['abteilung'],
                'fgNummer' => $_POST['fgNummer'],
                'marke' => $_POST['marke'],
                'modell' => $_POST['modell'],
                'farbe' => $_POST['farbe'],
                'aufnahmebereich' => $_POST['aufnahmebereich'],
                'bildNummer' => uniqid('bild_', true), // Generieren einer eindeutigen Bildnummer
                'liste_id' => $_POST['liste_id'],
                'bildPfad' => null
            ];
    
            // Speichern des Aufnahmebereichs in der Session
            $_SESSION['last_aufnahmebereich'] = $_POST['aufnahmebereich'];
    
            // Bild-Upload-Verarbeitung
            $imageUploaded = false; // Variable, um zu verfolgen, ob ein Bild hochgeladen wurde
            $uploadDir = dirname(__DIR__) . '/public/uploaded_files/'; // Absoluter Pfad zum Upload-Verzeichnis
            $relativeUploadDir = 'public/uploaded_files/'; // Relativer Pfad für die Datenbank
    
            // Überprüfen, ob ein Base64-kodiertes Bild hochgeladen wurde
            if (!empty($_POST['bildData'])) {
                $decoded_image = base64_decode(str_replace('data:image/png;base64,', '', $_POST['bildData']));
                $dest_path = $uploadDir . $data['bildNummer'] . '.png';
                if (file_put_contents($dest_path, $decoded_image)) {
                    $data['bildPfad'] = $relativeUploadDir . $data['bildNummer'] . '.png';
                    $imageUploaded = true;
                }
            } elseif (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
                // Überprüfen, ob ein Bild über das Formular hochgeladen wurde
                $fileTmpPath = $_FILES['bild']['tmp_name'];
                $fileName = $_FILES['bild']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
    
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $dest_path = $uploadDir . $data['bildNummer'] . '.' . $fileExtension;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $data['bildPfad'] = $relativeUploadDir . $data['bildNummer'] . '.' . $fileExtension;
                        $imageUploaded = true;
                    }
                }
            }
    
            // Falls kein Bild hochgeladen wurde
            if (!$imageUploaded) {
                // Hier keine Fehlermeldung setzen, nur weiterhin den Controller verwenden
            }
    
            // Fahrzeugdaten in der Datenbank speichern
            if ($this->model->createVehicle($data)) {
                if ($_POST['action'] == 'save_new') {
                    $_SESSION['success_message'] = "Fahrzeug erfolgreich hinzugefügt!";
                    header("Location: ../views/vehicles/create.php?liste_id=" . $_POST['liste_id']);
                } elseif ($_POST['action'] == 'save_close') {
                    $_SESSION['success_message'] = "Fahrzeug erfolgreich hinzugefügt!";
                    header("Location: ../views/lists/show.php?liste_id=" . $_POST['liste_id']);
                }
                exit();
            } else {
                // Hier keine Fehlermeldung setzen, nur weiterhin den Controller verwenden
            }
        }
    }
        
    // Methode zum Aktualisieren eines Fahrzeugs
    public function update() {
        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sammeln der Fahrzeugdaten aus dem POST-Array
            $data = [
                'id' => isset($_POST['id']) ? trim($_POST['id']) : null,
                'barcode' => isset($_POST['barcode']) ? trim($_POST['barcode']) : '',
                'barcode8' => isset($_POST['barcode8']) ? trim($_POST['barcode8']) : '',
                'abteilung' => isset($_POST['abteilung']) ? trim($_POST['abteilung']) : '',
                'fgNummer' => isset($_POST['fgNummer']) ? trim($_POST['fgNummer']) : '',
                'marke' => isset($_POST['marke']) ? trim($_POST['marke']) : '',
                'modell' => isset($_POST['modell']) ? trim($_POST['modell']) : '',
                'farbe' => isset($_POST['farbe']) ? trim($_POST['farbe']) : '',
                'aufnahmebereich' => isset($_POST['aufnahmebereich']) ? trim($_POST['aufnahmebereich']) : '',
                'bildPfad' => null
            ];

            $uploadDir = dirname(__DIR__) . '/public/uploaded_files/'; // Absoluter Pfad zum Upload-Verzeichnis
            $relativeUploadDir = 'public/uploaded_files/'; // Relativer Pfad für die Datenbank

            // Überprüfen, ob ein neues Bild hochgeladen wurde
            if (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['bild']['tmp_name'];
                $fileName = $_FILES['bild']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $data['bildNummer'] = uniqid('bild_', true);
                    $dest_path = $uploadDir . $data['bildNummer'] . '.' . $fileExtension;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $data['bildPfad'] = $relativeUploadDir . $data['bildNummer'] . '.' . $fileExtension;
                    }
                }
            }

            // Fahrzeugdaten in der Datenbank aktualisieren
            if ($this->model->updateVehicle($data)) {
                $_SESSION['success_message'] = "Das Fahrzeug wurde erfolgreich aktualisiert.";
                // Rückmeldung an den Client senden
                header('Content-Type: application/json'); // Stelle sicher, dass der Content-Type Header gesetzt ist
                echo json_encode(['success' => true, 'message' => "Das Fahrzeug wurde erfolgreich aktualisiert."]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => "Fehler: " . $this->model->getError()]);
            }
            exit();
        } else {
            echo "Ungültige Anfrage";
        }
    }

    // Methode zum Abrufen von Fahrzeugen anhand der Listen-ID
    public function getByListId($liste_id) {
        return $this->model->getByListId($liste_id);
    }

    // Methode zum Abrufen der Fahrzeugdetails
    public function getVehicleDetails() {
        header('Content-Type: application/json'); // Setze den Content-Type-Header

        $response = array('success' => false); // Initialisiere das Antwort-Array

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $barcode = $_GET['barcode'] ?? null;
            $barcode8 = $_GET['barcode8'] ?? null;
            $fgNummer = $_GET['fgNummer'] ?? null;
            $vehicle = null;

            // Suche in der Tabelle "bestandsfahrzeuge" nach dem Barcode
            if (isset($_GET['barcode'])) {
                $vehicle = $this->model->getVehicleByBarcode($barcode);
            }
            // Suche in der Tabelle "bestandsfahrzeuge" nach dem Barcode8
            elseif (isset($_GET['barcode8'])) {
                $vehicle = $this->model->getVehicleByBarcode8($barcode8);
            }
            // Suche in der Tabelle "bestandsfahrzeuge" nach der Fahrgestellnummer (fgNummer)
            elseif (isset($_GET['fgNummer'])) {
                $vehicle = $this->model->getVehicleByFgNummer($fgNummer);
            }

            // Wenn ein Fahrzeug gefunden wurde, geben wir die Daten zurück
            if ($vehicle) {
                $response['success'] = true;
                $response['vehicle'] = $vehicle;
            } else {
                $response['message'] = 'Fahrzeug nicht gefunden.';
            }
        } else {
            $response['message'] = 'Ungültige Anfrage.';
        }

        echo json_encode($response); // Gebe die JSON-Antwort zurück
    }    

    // Methode zum Löschen eines Fahrzeugs
    public function delete() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['id'];
            // Holen Sie sich die Fahrzeugdetails, um den Bildpfad zu bekommen
            $vehicle = $this->model->getVehicleById($id);
            if ($vehicle) {
                // Löschen Sie das Bild, wenn es existiert
                if (!empty($vehicle['bildPfad'])) {
                    $filePath = dirname(__DIR__) . '/' . $vehicle['bildPfad']; // Absoluter Pfad zum Bild
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                // Fahrzeugdaten in der Datenbank löschen
                if ($this->model->deleteVehicle($id)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Löschen fehlgeschlagen.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden.']);
            }
        }
    }

    public function showImages() {
        $vehicles = $this->model->getVehiclesWithListNumber();
        include '../views/images/index.php';
    }
}

// Instanziieren des VehicleControllers und Ausführen der entsprechenden Methode basierend auf der Aktion
$controller = new VehicleController($conn);

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'store':
            $controller->store();
            break;
        case 'update':
            $controller->update();
            break;
        case 'getVehicleDetails':
            $controller->getVehicleDetails();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'showImages':
            $controller->showImages();
            break;
    }
}
