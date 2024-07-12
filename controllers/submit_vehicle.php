<?php
// Verbindung einbinden
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formulardaten extrahieren
    $barcode = $_POST['barcode'];
    $barcode8 = $_POST['barcode8'];
    $abteilung = $_POST['abteilung'];
    $fgNummer = $_POST['fgNummer'];
    $marke = $_POST['marke'];
    $modell = $_POST['modell'];
    $farbe = $_POST['farbe'];
    $aufnahmebereich = $_POST['aufnahmebereich'];
    $liste_id = $_POST['liste_id'];
    $action = $_POST['action'];
    $bildData = $_POST['bildData'];

    // Debugging: Überprüfen der liste_id
    echo "Liste ID: " . htmlspecialchars($liste_id) . "<br>";

    // Überprüfen, ob die liste_id in der Tabelle listen existiert
    $stmt = $conn->prepare("SELECT id FROM listen WHERE id = ?");
    $stmt->bind_param("i", $liste_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Fehler: Die angegebene Liste ID existiert nicht.");
    }

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
    $bildNummer = null;
    $bildPfad = null;
    if (!empty($bildData)) {
        $bildNummer = uniqid('bild_', true); // Einzigartige Bildnummer generieren
        $uploadFileDir = '../uploaded_files/'; // Korrigierter Pfad
        $dest_path = $uploadFileDir . $bildNummer . '.png';

        $decoded_image = base64_decode(str_replace('data:image/png;base64,', '', $bildData));
        if (file_put_contents($dest_path, $decoded_image)) {
            $bildPfad = 'uploaded_files/' . $bildNummer . '.png'; // Relativer Pfad für Datenbank
        } else {
            echo 'Fehler beim Speichern des Bildes.';
        }
    } elseif (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bild']['tmp_name'];
        $fileName = $_FILES['bild']['name'];
        $fileSize = $_FILES['bild']['size'];
        $fileType = $_FILES['bild']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $bildNummer = uniqid('bild_', true); // Einzigartige Bildnummer generieren
            $uploadFileDir = '../uploaded_files/';
            $dest_path = $uploadFileDir . $bildNummer . '.' . $fileExtension;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $bildPfad = 'uploaded_files/' . $bildNummer . '.' . $fileExtension; // Relativer Pfad für Datenbank
                // Debugging: Überprüfen Sie den Dateipfad
                echo "Bild erfolgreich hochgeladen: $dest_path<br>";
            } else {
                echo 'Fehler beim Verschieben der hochgeladenen Datei.';
            }
        } else {
            echo 'Ungültige Dateierweiterung. Erlaubt sind nur: ' . implode(',', $allowedfileExtensions);
        }
    }

    // Debugging: Überprüfen der Bildpfad
    echo "BildPfad: " . htmlspecialchars($bildPfad) . "<br>";

    // SQL-Injection verhindern
    $stmt = $conn->prepare("INSERT INTO fahrzeuge (barcode, barcode8, abteilung, fgNummer, marke, modell, farbe, aufnahmebereich, bildNummer, liste_id, bildPfad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssis", $barcode, $barcode8, $abteilung, $fgNummer, $marke, $modell, $farbe, $aufnahmebereich, $bildNummer, $liste_id, $bildPfad);

    if ($stmt->execute() === TRUE) {
        if ($action == 'save_new') {
            // Formular wird geleert und die Seite bleibt, um ein neues Fahrzeug einzugeben
            header("Location: ../public/erfassen.php?liste_id=$liste_id");
        } elseif ($action == 'save_close') {
            // Weiter zur Aufnahmeliste
            header("Location: ../public/aufnahmelisten.php?liste_id=$liste_id");
        }
        exit();
    } else {
        echo "Fehler: " . $stmt->error;
    }

    // Statement schließen
    $stmt->close();
} else {
    // GET-Anfrage behandeln oder weiterleiten
    echo "Diese Seite sollte nur über ein Formular aufgerufen werden.";
}

// Verbindung schließen
$conn->close();
?>
