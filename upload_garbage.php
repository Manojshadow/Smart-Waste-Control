<?php
header('Content-Type: application/json');

$targetDir = "uploads/";
$response = ["success" => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'], $_POST['address'], $_FILES['image'])) {
        $userId = $_POST['user_id'];
        $address = $_POST['address'];
        $file = $_FILES['image'];

        $fileName = 'img_' . time() . '.jpg';
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Database connection
            $conn = new mysqli("localhost", "root", "", "smartwastecontrol");
            if ($conn->connect_error) {
                $response['message'] = "Database connection failed.";
                echo json_encode($response);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO garbage_requests (user_id, address, image_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $address, $fileName);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Uploaded and stored successfully.";
            } else {
                $response['message'] = "DB Insert failed.";
            }

            $stmt->close();
            $conn->close();
        } else {
            $response['message'] = "Image upload failed.";
        }
    } else {
        $response['message'] = "Missing parameters.";
    }
} else {
    $response['message'] = "Invalid request.";
}

echo json_encode($response);
?>
