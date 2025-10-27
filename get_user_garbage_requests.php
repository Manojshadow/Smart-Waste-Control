<?php
include 'config.php';

$user_id = $_GET['user_id'] ?? '';

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id']);
    exit;
}

$sql = "SELECT image_url, status, DATE_FORMAT(created_at, '%d/%m/%Y') as date FROM user_garbage_requests WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = [
        'image_url' => $row['image_url'],
        'status' => $row['status'],
        'date' => $row['date']
    ];
}

echo json_encode(['success' => true, 'requests' => $requests]);

$stmt->close();
$conn->close();
?>
