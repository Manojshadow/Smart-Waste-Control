<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$userID = $data["userID"] ?? null;

if (!$userID) {
    echo json_encode(["success" => false, "message" => "Missing userID"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, gender, email, phone, address FROM userprofile WHERE id = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode($user); // Flat JSON for SwiftUI
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
?>
