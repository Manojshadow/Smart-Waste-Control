<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "smartwastecontrol");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $address = $_POST['address'];
    $image_path = isset($_POST['image_path']) ? $_POST['image_path'] : null;

    $stmt = $conn->prepare("INSERT INTO locationdata (latitude, longitude, address, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ddss", $latitude, $longitude, $address, $image_path);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
