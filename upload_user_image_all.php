<?php
include "config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$uploadDir = "uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!is_writable($uploadDir)) {
    echo json_encode([
        "success" => false,
        "message" => "Upload directory is not writable",
        "directory" => realpath($uploadDir),
        "permissions" => substr(sprintf('%o', fileperms($uploadDir)), -4)
    ]);
    exit();
}

if (!isset($_POST['request_id']) || !isset($_FILES['image'])) {
    echo json_encode(["success" => false, "message" => "Missing request_id or image"]);
    exit();
}

$request_id = $_POST['request_id'];
$imageFile = $_FILES['image'];
$filename = uniqid() . "_" . basename($imageFile["name"]);
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($imageFile["tmp_name"], $targetPath)) {
    $sql = "UPDATE user_garbage_requests SET image_path = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $filename, $request_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Image uploaded", "image_url" => $targetPath]);
    } else {
        echo json_encode(["success" => false, "message" => "DB update failed"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Image save failed"]);
}

$conn->close();
?>
