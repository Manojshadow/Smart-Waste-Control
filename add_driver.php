<?php
include 'config.php';

$phone = '9876543210';
$plainPassword = 'driver123';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO drivers (phone, password) VALUES (?, ?)");
$stmt->execute([$phone, $hashedPassword]);

echo "Driver inserted.";
?>
