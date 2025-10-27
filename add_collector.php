<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "smartwastecontrol");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$name = $conn->real_escape_string($data["name"] ?? "");
$age = intval($data["age"] ?? 0);
$phone = $conn->real_escape_string($data["phone_number"] ?? "");

if (empty($name) || empty($phone) || $age <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$sql = "INSERT INTO admin_collectors (name, age, phone_number) VALUES ('$name', $age, '$phone')";
if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Collector added"]);
} else {
    echo json_encode(["success" => false, "message" => "Insert error: " . $conn->error]);
}
$conn->close();
?>
