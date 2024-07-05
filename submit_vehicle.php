<?php
// Verbindung einbinden
include 'config.php';

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
    $bildNummer = $_POST['bildNummer'];
    $liste_id = $_POST['liste_id'];
    $action = $_POST['action'];

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

    // SQL-Abfrage zum Einfügen der Daten (Prepared Statement)
    $stmt = $conn->prepare("INSERT INTO fahrzeuge (barcode, barcode8, abteilung, fgNummer, marke, modell, farbe, aufnahmebereich, bildNummer, liste_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $barcode, $barcode8, $abteilung, $fgNummer, $marke, $modell, $farbe, $aufnahmebereich, $bildNummer, $liste_id);

    if ($stmt->execute() === TRUE) {
        if ($action == 'save_new') {
            // Formular wird geleert und die Seite bleibt, um ein neues Fahrzeug einzugeben
            header("Location: index.php?liste_id=$liste_id");
        } elseif ($action == 'save_close') {
            // Weiter zur Aufnahmeliste
            header("Location: aufnahmelisten.php?liste_id=$liste_id");
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
    // Alternativ könnten Sie die Benutzer zur Index-Seite weiterleiten:
    // header("Location: index.php");
}

// Verbindung schließen
$conn->close();
?>