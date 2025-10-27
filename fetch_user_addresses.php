<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "localhost";
$user = "root";
$password = "";
$dbname = "smartwastecontrol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "addresses" => []]);
    exit();
}

$user_id = intval($_GET['user_id'] ?? 0);
if ($user_id === 0) {
    echo json_encode(["success" => false, "addresses" => []]);
    exit();
}

$sql = "SELECT address FROM user_addresses WHERE user_id = $user_id ORDER BY id DESC";
$result = $conn->query($sql);

$addresses = [];
while ($row = $result->fetch_assoc()) {
    $addresses[] = $row['address'];
}

echo json_encode(["success" => true, "addresses" => $addresses]);
$conn->close();
?>
