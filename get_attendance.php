<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// DB connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "smartwastecontrol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

$driverID = isset($_GET['driver_id']) ? intval($_GET['driver_id']) : 0;

if ($driverID <= 0) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT date, status FROM driverattendance WHERE driver_id = $driverID ORDER BY date DESC";
$result = $conn->query($sql);

$attendance = [];
while ($row = $result->fetch_assoc()) {
    $attendance[] = $row;
}

echo json_encode($attendance);
$conn->close();
?>
