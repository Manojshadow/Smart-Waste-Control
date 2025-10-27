<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$name    = $data["name"] ?? '';
$gender  = $data["gender"] ?? '';
$email   = $data["email"] ?? '';
$phone   = $data["phone"] ?? '';
$address = $data["address"] ?? '';

if (!$name || !$email) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO userprofile (name, gender, email, phone, address) VALUES (?, ?, ?, ?, ?)");
if ($stmt->execute([$name, $gender, $email, $phone, $address])) {
    echo json_encode(["success" => true, "userID" => $pdo->lastInsertId()]);
} else {
    echo json_encode(["success" => false, "message" => "Insert failed"]);
}
?>
