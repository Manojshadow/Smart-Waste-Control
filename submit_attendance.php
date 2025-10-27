<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "smartwastecontrol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['driver_id'], $data['date'], $data['status'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit();
}

$driverID = intval($data['driver_id']);
$date = $conn->real_escape_string($data['date']);
$status = $conn->real_escape_string($data['status']);

// Check if already marked
$checkSql = "SELECT * FROM driverattendance WHERE driver_id = $driverID AND date = '$date'";
$checkResult = $conn->query($checkSql);

if ($checkResult->num_rows > 0) {
    // Update
    $updateSql = "UPDATE driverattendance SET status = '$status' WHERE driver_id = $driverID AND date = '$date'";
    $response = $conn->query($updateSql)
        ? ["status" => "success", "message" => "Attendance updated."]
        : ["status" => "error", "message" => "Update failed: " . $conn->error];
} else {
    // Insert
    $insertSql = "INSERT INTO driverattendance (driver_id, date, status) VALUES ($driverID, '$date', '$status')";
    $response = $conn->query($insertSql)
        ? ["status" => "success", "message" => "Attendance submitted."]
        : ["status" => "error", "message" => "Insert failed: " . $conn->error];
}

echo json_encode($response);
$conn->close();
?>
