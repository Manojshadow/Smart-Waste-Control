<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "smartwastecontrol");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit;
}

$result = $conn->query("SELECT address FROM admin_addresses ORDER BY id DESC");
$addresses = [];

while ($row = $result->fetch_assoc()) {
    $addresses[] = $row['address'];
}

echo json_encode(["success" => true, "addresses" => $addresses]);

$conn->close();
?>
