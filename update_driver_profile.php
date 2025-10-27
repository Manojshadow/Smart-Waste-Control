<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$password = "";
$dbname = "smartwastecontrol";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id']) || !isset($input['name']) || !isset($input['gender']) ||
    !isset($input['email']) || !isset($input['phone']) || !isset($input['address'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields."
    ]);
    exit();
}

$id = intval($input['id']);
$name = $conn->real_escape_string($input['name']);
$gender = $conn->real_escape_string($input['gender']);
$email = $conn->real_escape_string($input['email']);
$phone = $conn->real_escape_string($input['phone']);
$address = $conn->real_escape_string($input['address']);

// Check if record exists
$sqlCheck = "SELECT id FROM driverprofile WHERE id = $id";
$result = $conn->query($sqlCheck);

if ($result && $result->num_rows > 0) {
    // Update existing record
    $sql = "UPDATE driverprofile SET name='$name', gender='$gender', email='$email', phone='$phone', address='$address' WHERE id=$id";
} else {
    // Insert new record
    $sql = "INSERT INTO driverprofile (id, name, gender, email, phone, address)
            VALUES ($id, '$name', '$gender', '$email', '$phone', '$address')";
}

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "status" => "success",
        "message" => "Profile saved successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
}

$conn->close();
