<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

require 'config.php'; // include DB connection

$data = json_decode(file_get_contents("php://input"), true);

$name = $data["name"] ?? '';
$gender = $data["gender"] ?? '';
$email = $data["email"] ?? '';
$phone = $data["phone"] ?? '';
$address = $data["address"] ?? '';

if ($name && $gender && $email && $phone && $address) {
    $stmt = $pdo->prepare("INSERT INTO user_profiles (name, gender, email, phone, address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $gender, $email, $phone, $address])) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
}
?>
