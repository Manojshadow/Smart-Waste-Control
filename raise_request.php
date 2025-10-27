<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'];
$address = $data['address'];

if (!$user_id || !$address) {
    echo json_encode(["success" => false, "message" => "Missing user ID or address"]);
    exit;
}

$sql = "INSERT INTO user_requests (user_id, address) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Address stored", "request_id" => $conn->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to store address"]);
}
?>
