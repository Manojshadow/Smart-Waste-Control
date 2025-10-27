<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$database = "smartwastecontrol";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$image = $data["image_name"];
$status = $data["status"];
$date = $data["collected_date"];
$driver_id = $data["driver_id"];

$sql = "INSERT INTO driver_garbage_requests (image_name, status, collected_date, driver_id)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $image, $status, $date, $driver_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Insert failed"]);
}

$stmt->close();
$conn->close();
?>
