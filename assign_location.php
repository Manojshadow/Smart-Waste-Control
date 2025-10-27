<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// DB connection
$host = "localhost";
$user = "root";
$password = "";  // XAMPP default password
$dbname = "smartwastecontrol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit();
}

// Read input JSON
$data = json_decode(file_get_contents("php://input"), true);

$collector = $conn->real_escape_string($data['collector'] ?? '');
$address = $conn->real_escape_string($data['address'] ?? '');

if ($collector && $address) {
    $sql = "INSERT INTO garbage_assignments (address, collector) VALUES ('$address', '$collector')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Assignment stored"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}

$conn->close();
?>
