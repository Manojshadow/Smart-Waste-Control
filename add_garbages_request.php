<?php
$host = "localhost";
$db = "your_database_name";
$user = "root";
$pass = "";

header('Content-Type: application/json');

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);

$userID = $data['user_id'];
$imageName = $data['image_name'];
$status = $data['status'];
$date = $data['date_posted'];

$sql = "INSERT INTO garbage_requests (user_id, image_name, status, date_posted) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $userID, $imageName, $status, $date);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$conn->close();
?>
