<?php
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $liste_id = $_POST['liste_id'];
    $barcode = trim($_POST['barcode']);
    $barcode8 = trim($_POST['barcode8']);
    $abteilung = trim($_POST['abteilung']);
    $fgNummer = trim($_POST['fgNummer']);
    $marke = trim($_POST['marke']);
    $modell = trim($_POST['modell']);
    $farbe = trim($_POST['farbe']);
    $aufnahmebereich = trim($_POST['aufnahmebereich']);

    // Validierung
    if (!preg_match('/^[A-Za-z0-9]{6,12}$/', $barcode)) {
        die('Ungültiger Barcode');
    }

    if (!preg_match('/^[A-Za-z0-9]{8}$/', $barcode8)) {
        die('Ungültiger Barcode8');
    }

    if (!preg_match('/^[A-Za-z0-9]{7}$/', $fgNummer)) {
        die('Ungültige Fahrgestellnummer');
    }

    if (empty($marke) || empty($modell) || empty($farbe)) {
        die('Marke, Modell und Farbe dürfen nicht leer sein');
    }

    // Bild hochladen
    $bildPfad = null;
    if (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
        // Altes Bild löschen
        $sql = "SELECT bildPfad FROM fahrzeuge WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicle = $result->fetch_assoc();
        if ($vehicle && $vehicle['bildPfad']) {
            unlink($vehicle['bildPfad']); // Altes Bild löschen
        }

        // Neues Bild hochladen
        $fileTmpPath = $_FILES['bild']['tmp_name'];
        $fileName = $_FILES['bild']['name'];
        $fileSize = $_FILES['bild']['size'];
        $fileType = $_FILES['bild']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $bildNummer = uniqid('bild_', true); // Einzigartige Bildnummer generieren
            $uploadFileDir = './uploaded_files/';
            $dest_path = $uploadFileDir . $bildNummer . '.' . $fileExtension;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $bildPfad = $dest_path;
                $stmt = $conn->prepare("UPDATE fahrzeuge SET barcode=?, barcode8=?, abteilung=?, fgNummer=?, marke=?, modell=?, farbe=?, aufnahmebereich=?, bildNummer=?, bildPfad=? WHERE id=?");
                $stmt->bind_param("ssssssssssi", $barcode, $barcode8, $abteilung, $fgNummer, $marke, $modell, $farbe, $aufnahmebereich, $bildNummer, $bildPfad, $id);
            } else {
                die('Fehler beim Verschieben der hochgeladenen Datei.');
            }
        } else {
            die('Ungültige Dateierweiterung. Erlaubt sind nur: ' . implode(',', $allowedfileExtensions));
        }
    } else {
        $stmt = $conn->prepare("UPDATE fahrzeuge SET barcode=?, barcode8=?, abteilung=?, fgNummer=?, marke=?, modell=?, farbe=?, aufnahmebereich=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $barcode, $barcode8, $abteilung, $fgNummer, $marke, $modell, $farbe, $aufnahmebereich, $id);
    }

    if ($stmt->execute() === TRUE) {
        echo "Erfolgreich aktualisiert";
    } else {
        die("Fehler: " . $stmt->error);
    }

    $stmt->close();
} else {
    die("Ungültige Anfrage");
}

$conn->close();
?>
