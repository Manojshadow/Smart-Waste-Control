<?php
header("Content-Type: application/json");
include 'db.php'; // make sure this connects to your MySQL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];

    $stmt = $conn->prepare("UPDATE garbage_requests SET status = 'RESOLVED' WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status updated."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update."]);
    }

    $stmt->close();
    $conn->close();
}
?>
