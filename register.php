<?php
header("Content-Type: application/json");
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$confirmPassword = trim($data['confirmPassword'] ?? '');

if (empty($username) || empty($email) || empty($password) || $password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
}
?>
