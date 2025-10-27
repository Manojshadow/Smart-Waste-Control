<?php
$host = "localhost";
$db = "your_database_name";
$user = "root";
$pass = "";

header('Content-Type: application/json');

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$userID = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$sql = "SELECT image_name, status, DATE_FORMAT(date_posted, '%d/%m/%Y') as date FROM garbage_requests WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();
$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);

$conn->close();
?>
