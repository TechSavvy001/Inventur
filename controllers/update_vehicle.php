<?php
include '../config/config.php';

// Daten aus dem POST-Request abrufen
$id = $_POST['id'];
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

// SQL-Update-Abfrage vorbereiten
$sql = "UPDATE fahrzeuge SET barcode=?, barcode8=?, abteilung=?, fgNummer=?, marke=?, modell=?, farbe=?, aufnahmebereich=?, bildNummer=? WHERE id=? AND liste_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssi", $barcode, $barcode8, $abteilung, $fgNummer, $marke, $modell, $farbe, $aufnahmebereich, $bildNummer, $id, $liste_id);

// Ausführen der SQL-Abfrage
if ($stmt->execute()) {
    echo "Änderungen gespeichert!";
} else {
    echo "Fehler beim Aktualisieren des Fahrzeugs: " . $stmt->error;
}

// Verbindung schließen
$conn->close();
?>
