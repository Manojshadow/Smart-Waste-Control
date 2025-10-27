<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "smartwastecontrol");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB connection failed: " . $conn->connect_error]));
}

// Fetch collectors from the database
$sql = "SELECT id, name, age, phone_number FROM admin_collectors ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["success" => false, "message" => "Query failed: " . $conn->error]);
    $conn->close();
    exit;
}

$collectors = [];
while ($row = $result->fetch_assoc()) {
    $collectors[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "age" => intval($row["age"]),
        "phone_number" => $row["phone_number"]
    ];
}

// Return the collectors in JSON format
echo json_encode(["success" => true, "collectors" => $collectors]);
$conn->close();
?>
