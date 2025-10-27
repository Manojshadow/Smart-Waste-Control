<?php
$pdo = new PDO("mysql:host=localhost;dbname=smartwastecontrol", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$phone = '1';
$password = password_hash('1', PASSWORD_DEFAULT); // encrypt password

$stmt = $pdo->prepare("INSERT INTO driverlogin (phone, password) VALUES (?, ?)");
$stmt->execute([$phone, $password]);

echo "Sample driver added.";
?>
