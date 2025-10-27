<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    $conn = new mysqli("localhost", "root", "", "smartwastecontrol");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "DB failed"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, image_path, status, DATE_FORMAT(date_created, '%d/%m/%Y') as date FROM garbage_requests WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $requests = [];

    while ($row = $result->fetch_assoc()) {
        $requests[] = [
            "id" => $row['id'],
            "image_url" => "http://localhost/smartwastecontrol/uploads/" . $row['image_path'],
            "status" => $row['status'],
            "date" => $row['date']
        ];
    }

    echo json_encode(["success" => true, "requests" => $requests]);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
