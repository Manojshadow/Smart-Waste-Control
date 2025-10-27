<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$db   = 'your_database_name';
$user = 'your_username';
$pass = 'your_password';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image upload
    $address = $_POST['address'] ?? 'No Address';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imgTmpPath = $_FILES['image']['tmp_name'];
        $imgType    = $_FILES['image']['type'];
        $imgContent = file_get_contents($imgTmpPath);

        $stmt = $conn->prepare("INSERT INTO uploaded_images (address, image, image_type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $address, $imgContent, $imgType);

        if ($stmt->execute()) {
            $lastId = $stmt->insert_id;
            $response['status'] = 'success';
            $response['message'] = 'Image uploaded successfully.';
            $response['image_url'] = $_SERVER['PHP_SELF'] . '?view=' . $lastId;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Upload failed: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No image uploaded.';
    }

    echo json_encode($response);
    exit;
}

// Handle image viewing by ID
if (isset($_GET['view'])) {
    $id = intval($_GET['view']);
    $stmt = $conn->prepare("SELECT image, image_type FROM uploaded_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($imageData, $imageType);
        $stmt->fetch();
        header("Content-Type: " . $imageType);
        echo $imageData;
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Image not found']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// If nothing matched
echo json_encode(['status' => 'idle', 'message' => 'Waiting for image upload or view']);
?>
