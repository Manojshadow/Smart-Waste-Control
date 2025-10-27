<?php
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['address']) || empty($input['address'])) {
    echo json_encode(["success" => false, "message" => "Address is required"]);
    exit;
}

$address = $input['address'];
$conn = new mysqli("localhost", "root", "", "smartwastecontrol");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO admin_addresses (address) VALUES (?)");
$stmt->bind_param("s", $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Address added successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add address"]);
}

$stmt->close();
$conn->close();
?>
