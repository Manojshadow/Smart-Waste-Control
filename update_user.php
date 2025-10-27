<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$id      = $data["id"] ?? null;
$name    = $data["name"] ?? '';
$gender  = $data["gender"] ?? '';
$email   = $data["email"] ?? '';
$phone   = $data["phone"] ?? '';
$address = $data["address"] ?? '';

if (!$id || !$name || !$email) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$stmt = $pdo->prepare("UPDATE userprofile SET name = ?, gender = ?, email = ?, phone = ?, address = ? WHERE id = ?");
if ($stmt->execute([$name, $gender, $email, $phone, $address, $id])) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}
?>
