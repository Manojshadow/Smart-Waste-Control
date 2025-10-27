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

$driver_id = $_GET['driver_id'];
$sql = "SELECT id, image_name, status, DATE_FORMAT(collected_date, '%d/%m/%Y') as collected_date
        FROM driver_garbage_requests WHERE driver_id = $driver_id ORDER BY collected_date DESC";

$result = $conn->query($sql);

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
$conn->close();
?>
