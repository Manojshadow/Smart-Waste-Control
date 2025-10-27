<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "smartwastecontrol";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => "false", "message" => "Database connection failed"]);
    exit();
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$phone = $data['phone'] ?? '';
$dob = $data['dob'] ?? '';
$age = $data['age'] ?? '';
$gender = $data['gender'] ?? '';
$blood_group = $data['blood_group'] ?? '';
$address = $data['address'] ?? '';
$licence = $data['licence'] ?? '';
$id_proof = $data['id_proof'] ?? '';
$password = $data['password'] ?? '';

// Validation check
if (!$name || !$phone || !$password) {
    echo json_encode(["success" => "false", "message" => "Missing required fields"]);
    exit();
}

// ✅ Check for duplicate phone
$check = $conn->prepare("SELECT id FROM admin_drivers WHERE phone = ?");
$check->bind_param("s", $phone);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(["success" => "false", "message" => "Phone number already registered"]);
    exit();
}

// ✅ Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ✅ Insert data
$stmt = $conn->prepare("INSERT INTO admin_drivers (name, phone, dob, age, gender, blood_group, address, licence, id_proof, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $name, $phone, $dob, $age, $gender, $blood_group, $address, $licence, $id_proof, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => "true", "message" => "Driver registered successfully"]);
} else {
    echo json_encode(["success" => "false", "message" => "Registration failed"]);
}

$stmt->close();
$conn->close();
?>
