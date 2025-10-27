<?php
header("Content-Type: application/json");
require 'config.php';

$userID = $_GET["user_id"] ?? null;

if (!$userID) {
    echo json_encode(["success" => false, "message" => "Missing user_id"]);
    exit;
}

$stmt = $pdo->prepare("SELECT address FROM user_addresses WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$userID]);

$addresses = [];
while ($row = $stmt->fetch()) {
    $addresses[] = $row["address"];
}

echo json_encode(["success" => true, "addresses" => $addresses]);
?>
