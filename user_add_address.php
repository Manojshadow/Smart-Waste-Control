<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// DB config
$host = "localhost";
$user = "root";
$password = "";
$dbname = "smartwastecontrol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = intval($data['user_id'] ?? 0);
$address = trim($conn->real_escape_string($data['address'] ?? ''));

if (empty($user_id) || empty($address)) {
    echo json_encode(["success" => false, "message" => "Missing user ID or address"]);
    exit();
}

$sql = "INSERT INTO user_addresses (user_id, address) VALUES ($user_id, '$address')";
if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Address saved"]);
} else {
    echo json_encode(["success" => false, "message" => "Insert failed"]);
}

$conn->close();
?>
