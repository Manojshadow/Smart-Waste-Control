<?php
header("Content-Type: application/json");
require "db_connection.php"; // your db connection script

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->user_id) || !isset($data->address)) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$userID = intval($data->user_id);
$address = trim($data->address);

// Insert
$stmt = $conn->prepare("INSERT INTO user_addresses (user_id, address) VALUES (?, ?)");
$stmt->bind_param("is", $userID, $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Address added"]);
} else {
    echo json_encode(["success" => false, "message" => "Insert failed"]);
}

$stmt->close();
$conn->close();
?>
