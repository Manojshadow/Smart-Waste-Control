<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'config.php';

$input = json_decode(file_get_contents("php://input"), true);
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$password = isset($input['password']) ? trim($input['password']) : '';

if (empty($phone) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Phone or password is missing."
    ]);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, password FROM driverlogin WHERE phone = ?");
    $stmt->execute([$phone]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($driver && password_verify($password, $driver['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "driverID" => (int)$driver['id']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid phone or password"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Server error: " . $e->getMessage()
    ]);
}
?>
