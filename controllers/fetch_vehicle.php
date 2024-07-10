<?php
// Verbindung einbinden
include '../config/config.php';

$response = array('success' => false);

if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];
    $stmt = $conn->prepare("SELECT * FROM Bestandsfahrzeuge WHERE barcode = ? OR barcode8 = ?");
    $stmt->bind_param("ss", $barcode, $barcode);
} elseif (isset($_GET['fgNummer'])) {
    $fgNummer = $_GET['fgNummer'];
    $stmt = $conn->prepare("SELECT * FROM Bestandsfahrzeuge WHERE fgNummer = ?");
    $stmt->bind_param("s", $fgNummer);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response = array_merge($response, $result->fetch_assoc());
    }
}

echo json_encode($response);
$stmt->close();
$conn->close();
?>
